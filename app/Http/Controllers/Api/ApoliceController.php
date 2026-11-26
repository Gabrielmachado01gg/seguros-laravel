<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApoliceRequest;
use App\Http\Resources\ApoliceResource;
use App\Models\Apolice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ApoliceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $apolices = Apolice::with('cliente')
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->query('ramo'), fn ($q, $v) => $q->where('ramo', $v))
            ->when($request->query('cliente_id'), fn ($q, $v) => $q->where('cliente_id', $v))
            ->orderBy('vigencia_fim')
            ->paginate(15);

        return ApoliceResource::collection($apolices);
    }

    public function store(ApoliceRequest $request): JsonResponse
    {
        $apolice = Apolice::create($request->validated())->load('cliente');

        return (new ApoliceResource($apolice))->response()->setStatusCode(201);
    }

    public function show(Apolice $apolice): ApoliceResource
    {
        return new ApoliceResource($apolice->load('cliente'));
    }

    public function update(ApoliceRequest $request, Apolice $apolice): ApoliceResource
    {
        $apolice->update($request->validated());

        return new ApoliceResource($apolice->load('cliente'));
    }

    public function destroy(Apolice $apolice): Response
    {
        $apolice->delete();

        return response()->noContent();
    }
}
