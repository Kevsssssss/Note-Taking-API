<?php

namespace App\Http\Controllers\Api;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::all();
        return response()->json($notes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get the raw content from the request body
        $input = $request->getContent();
        $data = json_decode($input, true); // Decode the JSON string into a PHP array

        // If decoding failed or the result is not an array, it's an invalid request
        if ($data === null || !is_array($data)) {
            return response()->json(['error' => 'Invalid JSON payload'], 400);
        }

        // Check if the payload is a single note or an array of notes
        if (array_keys($data) === range(0, count($data) - 1)) {
            // This is a numerically indexed array, so it's a batch of notes
            $notes = [];
            $errors = [];

            foreach ($data as $noteData) {
                $validator = Validator::make($noteData, [
                    'title' => 'required|string|max:255',
                    'body' => 'required|string',
                    'tags' => 'nullable|array',
                ]);

                if ($validator->fails()) {
                    $errors[] = $validator->errors();
                } else {
                    $notes[] = Note::create($noteData);
                }
            }

            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 400);
            }

            return response()->json($notes, 201);
        } else {
            // This is a single associative array, so it's a single note
            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'tags' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $note = Note::create($data);
            return response()->json($note, 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return response()->json($note, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $note->update($request->all());
        return response()->json($note, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(null, 204);
    }
}
