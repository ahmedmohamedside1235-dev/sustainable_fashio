<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Swap;
use App\Models\Transaction;
use App\Models\Transaction_items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionsController extends Controller
{
    public function index()
    {
        if (! Auth::guard('user')->check()) {
            return redirect()->route('login')->with('errorLogin', 'You must login first');
        }
        return view('users.collections');
    }

    // ==================== get all items =====================
    public function getItems()
    {
        $myId   = Auth::guard('user')->id();
        $myRole = Auth::guard('user')->user()->role;

        if ($myRole === 'seller') {
            $items = Item::with(['material', 'condition', 'seller'])
                ->where('seller_id', $myId)
                ->get();
        } else {
            $items = Item::with(['material', 'condition', 'seller'])
                ->where('seller_id', '!=', $myId)
                ->get();
        }

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'item_id'      => $item->item_id,
                'price'        => $item->price,
                'image'        => asset('storage/uploaded/' . $item->image),
                'condition'    => $item->condition->condition_name,
                'condition_id' => $item->condition_id,
                'material'     => $item->material->material_name,
                'material_id'  => $item->material_id,
                'category'     => $item->material->category,
                'owner_name'   => $item->seller->name,
                'owner_id'     => $item->seller_id,
                'is_mine'      => $item->seller_id === $myId,
            ];
        }

        return response()->json([
            'role'  => $myRole,
            'items' => $result,
        ]);
    }

    // ===================== delete my item =====================
    public function deleteMyItem($id)
    {
        $myId = Auth::guard('user')->id();
        $item = Item::where('item_id', $id)
            ->where('seller_id', $myId)
            ->first();
        if (! $item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        // delete all swap and transaction related to this item
        Swap::where('requested_item_id', $id)->delete();
        $txIds = Transaction_items::where('item_id', $id)->pluck('transaction_id');
        Transaction_items::where('item_id', $id)->delete();
        Transaction::whereIn('transaction_id', $txIds)->delete();
        $item->delete();
        return response()->json(['message' => 'Deleted!']);
    }

    // ===================== get all swaped item and requested item =====================
    public function getMyActions()
    {
        $myId           = Auth::guard('user')->id();
        $requestedIds   = [];
        $myTransactions = Transaction::where('buyer_id', $myId)
            ->where('status', 'pending')
            ->pluck('transaction_id')
            ->toArray();

        if (! empty($myTransactions)) {
            $requestedIds = Transaction_items::whereIn('transaction_id', $myTransactions)
                ->pluck('item_id')
                ->toArray();
        }

        $swappedIds = Swap::where('requester_id', $myId)
            ->where('status', 'pending')
            ->pluck('requested_item_id')
            ->toArray();

        return response()->json([
            'requested' => $requestedIds,
            'swapped'   => $swappedIds,
        ]);
    }

    // ===================== get requests =====================
    public function getRequests()
    {
        $myId   = Auth::guard('user')->id();
        $myRole = Auth::guard('user')->user()->role;

        if ($myRole === 'seller') {
            $transactions = Transaction::where('seller_id', $myId)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $transactions = Transaction::where('buyer_id', $myId)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $result = [];

        foreach ($transactions as $t) {
            $txItem = Transaction_items::where('transaction_id', $t->transaction_id)->first();
            $item   = $txItem ? Item::with(['condition', 'material'])
                ->find($txItem->item_id) : null;

            if ($myRole === 'seller') {
                $otherUser = \App\Models\UserData::find($t->buyer_id);
            } else {
                $otherUser = \App\Models\UserData::find($t->seller_id);
            }

            $result[] = [
                'transaction_id' => $t->transaction_id,
                'status'         => $t->status,
                'created_at'     => $t->created_at->format('d M Y'),
                'other_name'     => $otherUser ? $otherUser->name : 'Unknown',
                'item'           => $item ? [
                    'item_id'           => $item->item_id,
                    'price_at_purchase' => $txItem->price_at_purchase,
                    'image'             => asset('storage/uploaded/' . $item->image),
                    'condition'         => $item->condition->condition_name,
                    'material'          => $item->material->material_name,
                    'category'          => $item->material->category,
                ] : null,
            ];
        }

        return response()->json([
            'role'         => $myRole,
            'transactions' => $result,
        ]);
    }

    // ===================== update request =====================
    public function updateRequest(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
        ]);

        $myId        = Auth::guard('user')->id();
        $transaction = Transaction::where('transaction_id', $id)
            ->where('seller_id', $myId)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $transaction->status = $request->status;
        $transaction->save();

        return response()->json(['message' => 'Done!', 'status' => $transaction->status]);
    }

    // ==================== cancel request =====================
    public function cancelRequest($id)
    {
        $myId = Auth::guard('user')->id();

        // تأكد إن الـ transaction دي بتاعت الـ buyer ده وحالتها pending
        $transaction = Transaction::where('transaction_id', $id)
            ->where('buyer_id', $myId)
            ->where('status', 'pending')
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Not found or already closed'], 404);
        }

        $transaction->status = 'rejected';
        $transaction->save();
        return response()->json(['message' => 'Cancelled!']);
    }

    // ===================== store request =====================
    public function storeRequest(Request $request)
    {
        $request->validate([
            'item_id' => ['required', 'exists:items,item_id'],
        ]);

        $item = Item::find($request->item_id);

        $transaction = Transaction::create([
            'seller_id' => $item->seller_id,
            'buyer_id'  => Auth::guard('user')->id(),
            'status'    => 'pending',
        ]);

        Transaction_items::create([
            'transaction_id'    => $transaction->transaction_id,
            'item_id'           => $item->item_id,
            'price_at_purchase' => $item->price,
        ]);

        return response()->json(['message' => 'Request sent!']);
    }

    // ===================== store swap =====================
    public function storeSwap(Request $request)
    {
        $request->validate([
            'requested_item_id' => ['required', 'exists:items,item_id'],
            'cash_topup_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $item = Item::find($request->requested_item_id);

        Swap::create([
            'requester_id'      => Auth::guard('user')->id(),
            'receiver_id'       => $item->seller_id,
            'requested_item_id' => $item->item_id,
            'cash_topup_amount' => $request->cash_topup_amount,
            'status'            => 'pending',
        ]);

        return response()->json(['message' => 'Swap request sent!']);
    }

    // ===================== update swap =====================
    public function updateSwap(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
        ]);
        $myId = Auth::guard('user')->id();
        $swap = Swap::where('swap_id', $id)
            ->where('receiver_id', $myId)
            ->first();
        if (! $swap) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $swap->status = $request->status;
        $swap->save();
        return response()->json(['message' => 'Done!', 'status' => $swap->status]);
    }

    // ===================== get swaps =====================
    public function getSwaps()
    {
        $myId   = Auth::guard('user')->id();
        $myRole = Auth::guard('user')->user()->role;

        if ($myRole === 'seller') {
            $swaps = Swap::where('receiver_id', $myId)->orderBy('created_at', 'desc')->get();
        } else {
            $swaps = Swap::where('requester_id', $myId)->orderBy('created_at', 'desc')->get();
        }

        $result = [];
        foreach ($swaps as $s) {
            $item      = Item::with(['condition', 'material'])->find($s->requested_item_id);
            $otherUser = \App\Models\UserData::find($myRole === 'seller' ? $s->requester_id : $s->receiver_id);

            $result[] = [
                'swap_id'           => $s->swap_id,
                'status'            => $s->status,
                'cash_topup_amount' => $s->cash_topup_amount,
                'created_at'        => $s->created_at->format('d M Y'),
                'other_name'        => $otherUser ? $otherUser->name : 'Unknown',
                'item'              => $item ? [
                    'price'     => $item->price,
                    'image'     => asset('storage/uploaded/' . $item->image),
                    'condition' => $item->condition->condition_name,
                    'material'  => $item->material->material_name,
                    'category'  => $item->material->category,
                ] : null,
            ];
        }

        return response()->json(['role' => $myRole, 'swaps' => $result]);
    }

    //====================== cancel swap =====================
    public function cancelSwap($id)
    {
        $myId = Auth::guard('user')->id();
        $swap = Swap::where('swap_id', $id)->where('requester_id', $myId)->where('status', 'pending')->first();
        if (! $swap) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $swap->status = 'rejected';
        $swap->save();
        return response()->json(['message' => 'Cancelled!']);
    }

    // ===================== update item =====================
    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'price'        => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'min:1'],
            'condition_id' => ['required', 'exists:item_conditions,condition_id'],
            'material_id'  => ['required', 'exists:materials,material_id'],
            'image'        => ['nullable', 'image', 'max:5120'],
        ]);

        $myId = Auth::guard('user')->id();

        // check if item belongs to user
        $item = Item::where('item_id', $id)
            ->where('seller_id', $myId)
            ->first();

        if (! $item) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $item->price        = $request->price;
        $item->condition_id = $request->condition_id;
        $item->material_id  = $request->material_id;

        // edit image if new image uploaded
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
            . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploaded', $fileName, 'public');
            $item->image = $fileName;
        }

        $item->save();
        return response()->json(['message' => 'Item updated!']);
    }
}
