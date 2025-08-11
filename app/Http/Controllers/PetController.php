<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * US-MASC-03 · Listar mascotas del usuario autenticado
     */
    public function index()
    {
        $pets = Pet::where('user_id', auth()->id())->get()->map(function ($pet) {
            return [
                'id'          => $pet->id,
                'name'        => $pet->name,
                'species'     => $pet->species,
                'breed'       => $pet->breed,
                'birth_date'  => $pet->birth_date,
                'sex'         => $pet->sex,
                'color'       => $pet->color,
                'is_active'   => $pet->is_active,
                'photo_url'   => $pet->photo_path ? asset('storage/' . $pet->photo_path) : null,
            ];
        });

        return response()->json($pets);
    }

    /**
     * US-MASC-01 · Registrar una nueva mascota para el usuario autenticado
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'species'    => 'required|in:perro,gato,ave,otro',
            'breed'      => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'sex'        => 'nullable|in:macho,hembra,desconocido',
            'color'      => 'nullable|string|max:255',
        ]);
        /** @var \App\Models\User $user */
        $pet = Auth::user()->pets()->create($data);

        return response()->json([
            'message' => 'Mascota registrada correctamente.',
            'pet'     => $pet
        ], 201);
    }

    /**
     * US-MASC-04 · Subir o actualizar la foto de una mascota del usuario autenticado
     */
    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $pet = Pet::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pets', 'public');
            $pet->photo_path = $path;
            $pet->save();

            return response()->json([
                'message'    => 'Foto subida correctamente.',
                'photo_url'  => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No se subió ninguna imagen.'], 400);
    }

    // Pendiente:
    // - US-MASC-02 · Mostrar detalles de una mascota
    public function show($id)
    {
        $pet = Pet::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        return response()->json([
            'id'         => $pet->id,
            'name'       => $pet->name,
            'species'    => $pet->species,
            'breed'      => $pet->breed,
            'birth_date' => optional($pet->birth_date)->format('d-m-Y'),
            'sex'        => $pet->sex,
            'color'      => $pet->color,
            'is_active'  => $pet->is_active,
            'photo_url'  => $pet->photo_path ? asset('storage/' . $pet->photo_path) : null,
        ]);
    }

    // - US-MASC-05 · Editar datos de una mascota
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'species'    => 'sometimes|in:perro,gato,ave,otro',
            'breed'      => 'sometimes|nullable|string|max:255',
            'birth_date' => 'sometimes|nullable|date',
            'sex'        => 'sometimes|nullable|in:macho,hembra,desconocido',
            'color'      => 'sometimes|nullable|string|max:255',
            'is_active'  => 'sometimes|boolean',
        ]);

        $pet = Pet::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $pet->update($data);

        return response()->json([
            'message' => 'Mascota actualizada correctamente.',
            'pet'     => $pet
        ]);
    }

    // - US-MASC-06 · Eliminar mascota
}
