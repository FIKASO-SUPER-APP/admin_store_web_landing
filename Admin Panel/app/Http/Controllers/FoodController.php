<?php

namespace App\Http\Controllers;

class FoodController extends Controller
{

   public function __construct()
    {
        $this->middleware('auth');
    }
	 public function index($id='')
    {
   		return view("items.index")->with('id',$id);
    }

      public function edit($id)
    {
    	return view('items.edit')->with('id',$id);
    }

    public function create($id='')
    {
      return view('items.create')->with('id',$id);
    }
    public function createitem()
    {
      return view('items.create');
    }
    public function view($id)
    {
        return view('items.view')->with('id',$id);
    }

    public function import($id = '')
    {
        return view('items.import')->with('id', $id);
    }

    public function downloadTemplate()
    {
        $csvData = [
            ['name', 'price', 'discount', 'category_id', 'quantity', 'description', 'publish', 'calories', 'grams', 'fats', 'proteins', 'nonveg', 'take_away'],
            ['Pizza Margherita', '12.99', '0', 'category_id_here', '100', 'Classic Italian pizza with tomato and mozzarella', 'true', '250', '300', '8', '12', 'false', 'true'],
            ['Burger Classic', '8.50', '1.50', 'category_id_here', '50', 'Beef burger with lettuce and tomato', 'true', '400', '250', '15', '20', 'true', 'true']
        ];

        $filename = 'items_import_template.csv';
        
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

}
