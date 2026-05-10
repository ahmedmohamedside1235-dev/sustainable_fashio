<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Item_condition;
use App\Models\Material;
use App\Models\Swap;
use App\Models\Transaction;
use App\Models\Transaction_items;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        if (! Auth::guard('user')->check() || Auth::guard('user')->user()->role !== 'admin') {
            return redirect()->route('home');
        }
        return null;
    }

    // =====================
    //  dashboard
    // =====================
    public function dashboard()
    {
        $redirect = $this->checkAdmin();
        if ($redirect) {
            return $redirect;
        }

        // ── Overview Stats ──
        $stats = [
            'total_users'          => UserData::count(),
            'total_items'          => Item::count(),
            'total_transactions'   => Transaction::count(),
            'total_swaps'          => Swap::count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
        ];

        // ── Recent Transactions ──
        $recentTransactions = Transaction::with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ── Recent Swaps ──
        $recentSwaps = Swap::with(['requester', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ── Items Tab ──
        $items = Item::with(['seller', 'condition', 'material'])
            ->orderBy('created_at', 'desc')
            ->get();

        $itemStats = [
            'total' => Item::count(),
        ];

        $conditions = Item_condition::all();
        $materials  = Material::all();

        // ── Users Tab ──
        $users = UserData::orderBy('created_at', 'desc')->get();

        $userStats = [
            'total'   => UserData::count(),
            'buyers'  => UserData::where('role', 'buyer')->count(),
            'sellers' => UserData::where('role', 'seller')->count(),
            'admins'  => UserData::where('role', 'admin')->count(),
        ];

        // ── Transactions Tab ──
        $transactions = Transaction::with(['buyer', 'seller', 'items.item.material', 'items.item.condition'])
            ->orderBy('created_at', 'desc')
            ->get();

        $txStats = [
            'total'    => Transaction::count(),
            'pending'  => Transaction::where('status', 'pending')->count(),
            'accepted' => Transaction::where('status', 'accepted')->count(),
            'rejected' => Transaction::where('status', 'rejected')->count(),
        ];

        // ── Sustainability Tab ──
        $totalItems = Item::count() ?: 1;
        $totalTx    = Transaction::count() ?: 1;
        $totalSwap  = Swap::count() ?: 1;

        $materialStats = Material::withCount('items')
            ->having('items_count', '>', 0)
            ->orderBy('items_count', 'desc')
            ->get()
            ->map(function ($m) use ($totalItems) {
                $m->count      = $m->items_count;
                $m->percentage = round(($m->items_count / $totalItems) * 100);
                return $m;
            });

        $categoryStats = Material::selectRaw('category, count(*) as count')
            ->join('items', 'materials.material_id', '=', 'items.material_id')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($c) use ($totalItems) {
                $c->percentage = round(($c->count / $totalItems) * 100);
                return $c;
            });

        $swapAccepted = Swap::where('status', 'accepted')->count();
        $swapPending  = Swap::where('status', 'pending')->count();
        $swapRejected = Swap::where('status', 'rejected')->count();

        $sustainability = [
            'reused_items'       => $swapAccepted + $txStats['accepted'],
            'completed_swaps'    => $swapAccepted,
            'completed_requests' => $txStats['accepted'],
            'total_materials'    => Material::count(),
            'material_stats'     => $materialStats,
            'category_stats'     => $categoryStats,
            'tx_accepted_pct'    => round(($txStats['accepted'] / $totalTx) * 100),
            'tx_pending_pct'     => round(($txStats['pending'] / $totalTx) * 100),
            'tx_rejected_pct'    => round(($txStats['rejected'] / $totalTx) * 100),
            'swap_accepted'      => $swapAccepted,
            'swap_pending'       => $swapPending,
            'swap_rejected'      => $swapRejected,
            'swap_accepted_pct'  => round(($swapAccepted / $totalSwap) * 100),
            'swap_pending_pct'   => round(($swapPending / $totalSwap) * 100),
            'swap_rejected_pct'  => round(($swapRejected / $totalSwap) * 100),
        ];

        return view('admin.dashboard', compact(
            'stats', 'recentTransactions', 'recentSwaps',
            'items', 'itemStats', 'conditions', 'materials',
            'users', 'userStats',
            'transactions', 'txStats',
            'sustainability'
        ));
    }

    // =====================
    // delete item
    // =====================
    public function deleteItem($id)
    {
        $redirect = $this->checkAdmin();
        if ($redirect) {
            return $redirect;
        }
        Item::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted!']);
    }

    // =====================
    // update user
    // =====================
    public function updateUser(Request $request, $id)
    {
        $redirect = $this->checkAdmin();
        if ($redirect) {
            return $redirect;
        }

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $id . ',user_id'],
            'phone' => ['required', 'string'],
            'role'  => ['required', 'in:buyer,seller,admin'],
        ]);

        $user        = UserData::findOrFail($id);
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role  = $request->role;
        $user->save();

        return response()->json(['message' => 'Updated!']);
    }

    // =====================
    // delete user
    // =====================
    public function deleteUser($id)
    {
        $redirect = $this->checkAdmin();
        if ($redirect) {
            return $redirect;
        }

        // if user admin
        if ($id == Auth::guard('user')->id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }

        // get user
        $user = UserData::findOrFail($id);

        // get items for user
        $itemIds = Item::where('seller_id', $id)->pluck('item_id');

        // get all swap and delete it
        Swap::where('requester_id', $id)
            ->orWhere('receiver_id', $id)
            ->orWhereIn('requested_item_id', $itemIds)
            ->delete();

        // get transaction id for user
        $transactionIds = Transaction::where('buyer_id', $id)
            ->orWhere('seller_id', $id)
            ->pluck('transaction_id');

        // delete all transaction
        Transaction_items::whereIn('transaction_id', $transactionIds)->delete();
        Transaction::whereIn('transaction_id', $transactionIds)->delete();

        // delete item
        Item::where('seller_id', $id)->delete();

        // delete user
        $user->delete();

        return response()->json(['message' => 'Deleted!']);
    }
}
