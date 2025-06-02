<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateViveiro;
use App\Models\Biometria;
use App\Models\Viveiro;
use Illuminate\Http\Request;

class ViveiroController extends Controller
{
    public readonly Viveiro $viveiro;
    public function __construct()
    {
        $this->viveiro = new Viveiro();
    }
    

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'width' => 'required|numeric',
        'length' => 'required|numeric',
    ]);

    $data['area'] = $data['width'] * $data['length'];

    $viveiro = $this->viveiro->create($data);

    return response()->json(['message' => 'Viveiro criado com sucesso', 'data' => $viveiro], 201);
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   

       $viveiro = Viveiro::select(
    'viveiros.id',
    'viveiros.name',
    'viveiros.area',
    'biometrias.id as biometria_id',
    'biometrias.date as date',
    'biometrias.image as image',
    'biometrias.shrimp_weight as gramatura'
)
->leftJoin('biometrias', function($join) {
    $join->on('viveiros.id', '=', 'biometrias.viveiro_id')
         ->whereRaw('biometrias.date = (SELECT MAX(date) FROM biometrias WHERE viveiro_id = viveiros.id)');
})
->where('viveiros.id', $id) 
->first(); 
 
        return view("viveiros.show", ['viveiro' => $viveiro]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Viveiro $viveiro)
    {
        return view("viveiros.edit", ["viveiro" => $viveiro]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(['nome' => 'required|string|max:255',
            'largura' => 'required|numeric|gt:0|lte:999',
            'comprimento' => 'required|numeric|gt:0|lte:999',]);
        $updated = $this->viveiro->where('id', $id)->update(['name' => $request->input('nome'),
            'width' => $request->input('largura'),
            'length' => $request->input('comprimento'),
            'area' => $request->input('largura') * $request->input('comprimento')], $request->except('_token', '_method'));
        if($updated){
            return redirect()->route("viveiros.index")->with('success', 'Viveiro Editado com Sucesso' );
        }
        return redirect()->back()->with('message', 'Erro ao editar' );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->viveiro->where('id', $id)->delete();
        return redirect()->route("viveiros.index")->with('success', 'Viveiro deletado com Sucesso' );
    }
}
