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
    // Check if the logged-in user is an admin
    private function checkAdmin()
    {
        if (! Auth::guard('user')->check() || Auth::guard('user')->user()->role !== 'admin') {
            return redirect()->route('home');
        }
        return null;
    }

    // =====================
    // Main dashboard
    // =====================
    public function dashboard()
    {
        $redirect = $this->checkAdmin();
        if ($redirect) return $redirect;

        // Overview counts
        $stats = [
            'total_users'          => UserData::count(),
            'total_items'          => Item::count(),
            'total_transactions'   => Transaction::count(),
            'total_swaps'          => Swap::count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
        ];

        // Last 5 transactions with buyer and seller names
        $recentTransactions = Transaction::with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Last 5 swaps with requester and receiver names
        $recentSwaps = Swap::with(['requester', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // All items with related seller, condition, material
        $items      = Item::with(['seller', 'condition', 'material'])->orderBy('created_at', 'desc')->get();
        $itemStats  = ['total' => Item::count()];
        $conditions = Item_condition::all();
        $materials  = Material::all();

        // All users ordered by newest
        $users     = UserData::orderBy('created_at', 'desc')->get();
        $userStats = [
            'total'   => UserData::count(),
            'buyers'  => UserData::where('role', 'buyer')->count(),
            'sellers' => UserData::where('role', 'seller')->count(),
            'admins'  => UserData::where('role', 'admin')->count(),
        ];

        // All transactions with related buyer, seller, items
        $transactions = Transaction::with(['buyer', 'seller', 'items.item.material', 'items.item.condition'])
            ->orderBy('created_at', 'desc')
            ->get();

        $txStats = [
            'total'    => Transaction::count(),
            'pending'  => Transaction::where('status', 'pending')->count(),
            'accepted' => Transaction::where('status', 'accepted')->count(),
            'rejected' => Transaction::where('status', 'rejected')->count(),
        ];

        // Sustainability calculations
        $totalItems = Item::count() ?: 1;
        $totalTx    = Transaction::count() ?: 1;
        $totalSwap  = Swap::count() ?: 1;

        // Count how many items each material has + calculate percentage
        $materialStats = Material::withCount('items')
            ->having('items_count', '>', 0)
            ->orderBy('items_count', 'desc')
            ->get()
            ->map(function ($m) use ($totalItems) {
                $m->count      = $m->items_count;
                $m->percentage = round(($m->items_count / $totalItems) * 100);
                return $m;
            });

        // Count items grouped by material category
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
            'tx_pending_pct'     => round(($txStats['pending']  / $totalTx) * 100),
            'tx_rejected_pct'    => round(($txStats['rejected'] / $totalTx) * 100),
            'swap_accepted'      => $swapAccepted,
            'swap_pending'       => $swapPending,
            'swap_rejected'      => $swapRejected,
            'swap_accepted_pct'  => round(($swapAccepted / $totalSwap) * 100),
            'swap_pending_pct'   => round(($swapPending  / $totalSwap) * 100),
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
    // Search users by name or email (server-side)
    // =====================
    public function searchUsers(Request $request)
    {
        $redirect = $this->checkAdmin();
        if ($redirect) return $redirect;

        $query = $request->input('query', '');
        $role  = $request->input('role', '');

        // Start building the query
        $users = UserData::query();

        // Filter by name or email if search term exists
        if ($query) {
            $users->where(function ($q) use ($query) {
                $q->where('name',  'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
            });
        }

        // Filter by role if selected
        if ($role) {
            $users->where('role', $role);
        }

        $result = $users->orderBy('created_at', 'desc')->get();

        // Return each user's data as JSON
        return response()->json($result->map(function ($u) {
            return [
                'user_id'    => $u->user_id,
                'name'       => $u->name,
                'email'      => $u->email,
                'phone'      => $u->phone,
                'role'       => $u->role,
                'created_at' => $u->created_at->format('d M Y'),
            ];
        }));
    }

    // =====================
    // Export sustainability report as PDF
    // =====================
    public function exportPdf()
    {
        $redirect = $this->checkAdmin();
        if ($redirect) return $redirect;

        // Collect all data needed for the PDF
        $totalItems = Item::count() ?: 1;
        $totalTx    = Transaction::count() ?: 1;
        $totalSwap  = Swap::count() ?: 1;

        $swapAccepted = Swap::where('status', 'accepted')->count();
        $swapPending  = Swap::where('status', 'pending')->count();
        $swapRejected = Swap::where('status', 'rejected')->count();

        $txAccepted = Transaction::where('status', 'accepted')->count();
        $txPending  = Transaction::where('status', 'pending')->count();
        $txRejected = Transaction::where('status', 'rejected')->count();

        // Material usage stats
        $materialStats = Material::withCount('items')
            ->having('items_count', '>', 0)
            ->orderBy('items_count', 'desc')
            ->get();

        // Category stats
        $categoryStats = Material::selectRaw('category, count(*) as count')
            ->join('items', 'materials.material_id', '=', 'items.material_id')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // Build the HTML content for the PDF manually (no external library needed)
        $html = view('admin.pdf-report', compact(
            'totalItems', 'swapAccepted', 'swapPending', 'swapRejected',
            'txAccepted', 'txPending', 'txRejected',
            'materialStats', 'categoryStats'
        ))->render();

        // Return HTML as downloadable file with .html extension
        // (acts as a printable report the user can save as PDF from browser)
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="sustainability-report.html"');
    }

    // =====================
    // Delete item and all related data
    // =====================
    public function deleteItem($id)
    {
        $redirect = $this->checkAdmin();
        if ($redirect) return $redirect;

        // Delete swaps linked to this item
        Swap::where('requested_item_id', $id)->delete();

        // Delete transaction items then their transactions
        $txIds = Transaction_items::where('item_id', $id)->pluck('transaction_id');
        Transaction_items::where('item_id', $id)->delete();
        Transaction::whereIn('transaction_id', $txIds)->delete();

        Item::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted!']);
    }

    // =====================
    // Update user info
    // =====================
    public function updateUser(Request $request, $id)
    {
        $redirect = $this->checkAdmin();
        if ($redirect) return $redirect;

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
    // Delete user and all related data
    // =====================
    public function deleteUser($id)
    {
        $redirect = $this->checkAdmin();
        if ($redirect) return $redirect;

        // Prevent admin from deleting their own account
        if ($id == Auth::guard('user')->id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }

        $user    = UserData::findOrFail($id);
        $itemIds = Item::where('seller_id', $id)->pluck('item_id');

        // Delete all swaps related to user or their items
        Swap::where('requester_id', $id)
            ->orWhere('receiver_id', $id)
            ->orWhereIn('requested_item_id', $itemIds)
            ->delete();

        // Delete all transactions related to user
        $transactionIds = Transaction::where('buyer_id', $id)
            ->orWhere('seller_id', $id)
            ->pluck('transaction_id');

        Transaction_items::whereIn('transaction_id', $transactionIds)->delete();
        Transaction::whereIn('transaction_id', $transactionIds)->delete();

        // Delete user's items then the user
        Item::where('seller_id', $id)->delete();
        $user->delete();

        return response()->json(['message' => 'Deleted!']);
    }
}