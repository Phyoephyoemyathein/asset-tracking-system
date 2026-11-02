<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('assets')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories']);
        
        $category = Category::create($request->only('name'));

        // **Category အသစ်ဆောက်လုပ်ခြင်း Log မှတ်တမ်းတင်ရန်**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created',
            'subject' => 'Category',
            'description' => auth()->user()->name . ' added a new category: ' . $category->name
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function destroy(Category $category)
    {
        // ပစ္စည်းတွေနဲ့ တွဲနေရင် ဖျက်လို့မရအောင် စစ်ဆေးနိုင်ပါတယ်
        if($category->assets()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category containing assets.');
        }
        
        $categoryName = $category->name;
        $category->delete();

        // **Category ဖျက်ဆီးခြင်း Log မှတ်တမ်းတင်ရန်**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'subject' => 'Category',
            'description' => auth()->user()->name . ' deleted the category: ' . $categoryName
        ]);

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ]);

        $oldName = $category->name;
        $category->update($request->only('name'));

        // **Category အချက်အလက် ပြင်ဆင်ခြင်း Log မှတ်တမ်းတင်ရန်**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated',
            'subject' => 'Category',
            'description' => auth()->user()->name . ' updated category from "' . $oldName . '" to "' . $category->name . '"'
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }
}