<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Item_condition;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddItemController extends Controller
{
    // Show the form to add a new item and check if the user is a seller
    public function add_item()
    {
        if (!Auth::guard('user')->check() || Auth::guard('user')->user()->role != 'seller') {
            return redirect()->route('home')->with('errorAdd', 'Please login with a seller account to access this page');
        }
        return view('users.addItem');
    }

    // Handle the form submission to add a new item
    public function add_item_store(Request $request)
    {
        // validate the incoming request data
        $request->validate([
            "price"    => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'gt:0'],
            "status"   => ['required', 'in:new,good,fair'],
            "category" => ['required', 'in:shirts,pants,dresses,jackets'],
            "material" => ['required', 'in:cotton,polyester,wool,denim,leather,silk,nylon,linen'],
            "image"    => ['required','image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        //* Add material item in table material
        $material                = new Material();
        $material->material_name = $request->material;
        $material->category      = $request->category;
        $material->save();

        //* Add item condition item in table item_condition
        $Item_condition                 = new Item_condition();
        $Item_condition->condition_name = $request->status;
        $Item_condition->save();

        //* Add item in table Items
        $item              = new Item();
        $item->seller_id   = Auth::guard('user')->user()->user_id;
        $item->material_id = $material->material_id;
        $item->condition_id = $Item_condition->condition_id;
        $item->price = $request->price;
        $item->image = $this->upload_image($request,'image');
        $item->save();

        return redirect()->back()->with('success', 'New item has been added successfully');
    }

    // Handle the image upload and return the file name to be stored in the database
    private function upload_image(Request $request, $name)
    {
        $file             = $request->file('image');
        $fileName         = $file->getClientOriginalName();
        $fileExtension    = $file->getClientOriginalExtension();
        $fileNameToUpload = pathinfo($fileName, PATHINFO_FILENAME) . '_' . time() . '.' . $fileExtension;
        $file->storeAs('uploaded', $fileNameToUpload, 'public');
        return $fileNameToUpload;
    }
}
