<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralJsonException;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $page_size = $request->get('page_size') ?? 5;
        $stores = Store::query()->paginate($page_size);
        return StoreResource::collection($stores);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStoreRequest $request
     * @return Response
     */
    public function store(StoreStoreRequest $request)
    {
        $data = $request->all();
        return DB::transaction(function () use ($data) {
            $store = Store::query()->create([
                'name' => $data['name'],
                'user_id'=>$data['user_id']
            ]);
            throw_if(!$store, GeneralJsonException::class, 'Failed to create. ');
            if($productIds = data_get($data, 'product_id')){
                $store->products()->sync($productIds);
            }
            return new StoreResource($store);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param Store $store
     * @return StoreResource
     */
    public function show(Store $store)
    {
        return new StoreResource($store);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStoreRequest $request
     * @param Store $store
     * @return Response
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        $data = $request->all();
        return DB::transaction(function () use($store, $data) {
            $updated = $store->update([
                'name' => $data['name'],
                'user_id'=>$data['user_id']
            ]);

            throw_if(!$updated, GeneralJsonException::class, 'Failed to update post');

            if($userIds = data_get($data, 'user_ids')){
                $store->products()->sync($userIds);
            }
            return (new StoreResource($store))->additional(['message' => 'Updated successfully']);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Store $store
     * @return Response
     */
    public function destroy(Store $store)
    {
        return DB::transaction(function () use($store) {
            $deleted = $store->forceDelete();

            throw_if(!$deleted, \Exception::class, "Cannot delete Store.");

            return response()->json(['message'=>'Data delete successfully'], 204);
        });
    }

    /**
     * Search products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $data = $request->all();
        $stores = Store::query()->where('name', 'like', '%'.$data['name'].'%')
            ->paginate(2);

        return response()->json($stores, 200);
    }
}
