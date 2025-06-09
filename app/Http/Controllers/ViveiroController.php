<?php

namespace App\Http\Controllers;

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
     * Listar todos os viveiros com a última biometria
     */
    public function index()
    {
        $viveiros = Viveiro::with('latestBiometria')->get();

        return response()->json([
            'success' => true,
            'data' => $viveiros
        ]);
    }

    /**
     * Criar novo viveiro
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

        return response()->json([
            'success' => true,
            'message' => 'Viveiro criado com sucesso',
            'data' => $viveiro
        ], 201);
    }

    /**
     * Mostrar detalhes de um viveiro com última biometria
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

        if (!$viveiro) {
            return response()->json([
                'success' => false,
                'message' => 'Viveiro não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $viveiro
        ]);
    }

    /**
     * Atualizar viveiro
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'largura' => 'required|numeric|gt:0|lte:999',
            'comprimento' => 'required|numeric|gt:0|lte:999',
        ]);

        $viveiro = $this->viveiro->find($id);

        if (!$viveiro) {
            return response()->json([
                'success' => false,
                'message' => 'Viveiro não encontrado'
            ], 404);
        }

        $viveiro->update([
            'name' => $data['nome'],
            'width' => $data['largura'],
            'length' => $data['comprimento'],
            'area' => $data['largura'] * $data['comprimento'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Viveiro atualizado com sucesso',
            'data' => $viveiro
        ]);
    }

    /**
     * Deletar viveiro
     */
    public function destroy($id)
    {
        $viveiro = $this->viveiro->find($id);

        if (!$viveiro) {
            return response()->json([
                'success' => false,
                'message' => 'Viveiro não encontrado'
            ], 404);
        }

        $viveiro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Viveiro deletado com sucesso'
        ]);
    }  
        public function destroyQuery(Request $request)
    {
        $id = $request->query('id'); // pega o ?id=123 da URL

        if (!$id) {
            return response()->json(['message' => 'ID não fornecido'], 400);
        }

        $viveiro = Viveiro::find($id);

        if (!$viveiro) {
            return response()->json(['message' => 'Viveiro não encontrado'], 404);
        }

        $viveiro->delete();

        return response()->json(['message' => 'Viveiro deletado com sucesso']);
    }

}
