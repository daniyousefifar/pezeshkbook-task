<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLabyrinthRequest;
use App\Http\Resources\LabyrinthCollection;
use App\Http\Resources\LabyrinthResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LabyrinthController extends Controller
{
    public function index(): LabyrinthCollection
    {
        $labyrinths = Auth::user()->labyrinths()->paginate(15);

        return new LabyrinthCollection($labyrinths);
    }

    public function store(CreateLabyrinthRequest $request): JsonResponse
    {
        $labyrinth = Auth::user()->labyrinths()->create([
            'dimensions' => [
                'width' => $request->input('dimensions.width'),
                'height' => $request->input('dimensions.height'),
            ],
        ]);

        for ($xIndex = 1; $xIndex <= $request->input('dimensions.width'); $xIndex++) {
            for ($yIndex = 1; $yIndex <= $request->input('dimensions.height'); $yIndex++) {
                $labyrinth->blocks()->create([
                    'x' => $xIndex,
                    'y' => $yIndex,
                    'passable' => true,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Labyrinth created successfully.',
            'data' => [
                'labyrinth_id' => $labyrinth->id,
            ]
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, $id): LabyrinthResource
    {
        $labyrinth = Auth::user()->labyrinths()->findOrFail($id);

        return new LabyrinthResource($labyrinth);
    }

    public function playfield(Request $request, $id, $x, $y, $type): JsonResponse
    {
        $labyrinth = Auth::user()->labyrinths()->findOrFail($id);

        if ($x > $labyrinth->dimensions['width'] || $y > $labyrinth->dimensions['height']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your block coordinates are not valid coordinates for the dimensions of this labyrinth.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (is_null($labyrinth->start) || is_null($labyrinth->end)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a complete pair of coordinates for the start and end points. Then try again.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($type == 'filled' && ($x === $labyrinth->start['x'] && $y === $labyrinth->start['y'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'The Starting point cannot be filled.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($type == 'filled' && ($x === $labyrinth->end['x'] && $y === $labyrinth->end['y'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'The Ending point cannot be filled.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $labyrinth->blocks()->updateOrCreate(
            ['x' => $x, 'y' => $y],
            ['x' => $x, 'y' => $y, 'passable' => $type == 'empty']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Your block has been successfully saved.',
        ], Response::HTTP_OK);
    }

    public function start(Request $request, $id, $x, $y): JsonResponse
    {
        $labyrinth = Auth::user()->labyrinths()->findOrFail($id);

        if ($x > $labyrinth->dimensions['width'] || $y > $labyrinth->dimensions['height']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your start coordinates are not valid coordinates for the dimensions of this labyrinth.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!is_null($labyrinth->end) && ($x === $labyrinth->end['x'] && $y === $labyrinth->end['y'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your start and end coordinates are the same.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($labyrinth->blocks()->where(['x' => $x, 'y' => $y, 'passable' => false])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The starting point cannot be a filled block.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $labyrinth->update([
            'start' => [
                'x' => $x,
                'y' => $y,
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'The starting coordinates were successfully registered.',
        ], Response::HTTP_OK);
    }

    public function end(Request $request, $id, $x, $y): JsonResponse
    {
        $labyrinth = Auth::user()->labyrinths()->findOrFail($id);

        if ($x > $labyrinth->dimensions['width'] || $y > $labyrinth->dimensions['height']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your end coordinates are not valid coordinates for the dimensions of this labyrinth.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!is_null($labyrinth->start) && ($x === $labyrinth->start['x'] && $y === $labyrinth->start['y'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your start and end coordinates are the same.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($labyrinth->blocks()->where(['x' => $x, 'y' => $y, 'passable' => false])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The ending point cannot be a filled block.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $labyrinth->update([
            'end' => [
                'x' => $x,
                'y' => $y,
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'The Ending coordinates were successfully registered.',
        ], Response::HTTP_OK);
    }

    public function solution(Request $request, $id): JsonResponse
    {
        $labyrinth = Auth::user()->labyrinths()->findOrFail($id);

        if (is_null($labyrinth->start) || is_null($labyrinth->end)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a complete pair of coordinates for the start and end points. Then try again.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $solution = (new \App\Services\Labyrinth($labyrinth))->getSolution();

            return response()->json([
                'status' => 'success',
                'message' => 'Your request has been successfully processed.',
                'data' => $solution,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'The server is currently unable to process your request. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
