<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class UniversalCrudController extends Controller
{
    protected $model;
    protected $table;
    protected $fields;

    public function __construct($model, $table, $fields)
    {
        $this->model = $model;
        $this->table = $table;
        $this->fields = $fields;
    }

    public function index(Request $request)
    {

        $entityName = Str::snake(class_basename($this->model));
        $fieldName = $this->fields[1] ?? 'name'; 
        $modelName = Str::title(Str::replace('_', ' ', $entityName));

        if ($request->ajax()) {
            $data = $this->model::select($this->fields);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    return '
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-id="'.$row->id.'"><i class="ti ti-edit fs-4"></i></button>
                        <button class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-id="'.$row->id.'"><i class="ti ti-trash fs-4"></i></button>
                    ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view("admin.master.index", compact('entityName', 'fieldName', 'modelName'));
    }

    public function show($id)
    {
        $data = $this->model::findOrFail($id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            $this->fields[1] => "required|unique:{$this->table},{$this->fields[1]}"
        ]);

        $this->model::create([$this->fields[1] => $request->{$this->fields[1]}]);

        return response()->json(['success' => ucfirst($this->table).' added successfully!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            $this->fields[1] => "required|unique:{$this->table},{$this->fields[1]},".$id
        ]);

        $data = $this->model::findOrFail($id);
        $data->update([$this->fields[1] => $request->{$this->fields[1]}]);

        return response()->json(['success' => ucfirst($this->table).' updated successfully!']);
    }

    public function destroy($id)
    {
        $this->model::findOrFail($id)->delete();
        return response()->json(['success' => ucfirst($this->table).' deleted successfully!']);
    }
}
