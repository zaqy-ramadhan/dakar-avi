<?php

namespace App\Http\Controllers;

use App\Models\Disnaker;
use Illuminate\Http\Request;

class DisnakerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $disnaker = Disnaker::first();
        return view('admin.disnaker.form', compact('disnaker'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:255'
        ]);

        Disnaker::create($request->all());

        return redirect()->route('disnaker.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disnaker $disnaker)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:255'
        ]);

        $disnaker = Disnaker::findOrFail($request->nip);
        $disnaker->update($request->all());

        return redirect()->route('disnaker.index')->with('success', 'Data berhasil diupdate');
    }
}