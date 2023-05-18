<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of Product.
     *
     * Gets a list of Product.
     *
     * @queryParam page_size int Size per page. Defaults to 20. Example: 20
     * @queryParam page int Page to view. Example: 1
     *
     * @apiResourceCollection App\Http\Resources\ProductResource
     * @apiResourceModel App\Models\Product
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $page_size = $request->get('page_size') ?? 5;
        $products = Product::query()->paginate($page_size);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $request
     * @return ProductResource
     * @throws ValidationException
     */
    public function store(StoreProductRequest $request): ProductResource
    {
        $data = $request->all();
        return DB::transaction(function () use ($data) {
            $product = Product::query()->create([
                'name' => $data['name'],
                'price'=>$data['price'],
                'user_id'=>$data['user_id']
            ]);
            throw_if(!$product, \Exception::class, 'Failed to create. ');
            if($storeIds = data_get($data, 'store_id')){
                $product->stores()->sync($storeIds);
            }
            return new ProductResource($product);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return ProductResource
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->all();
        return DB::transaction(function () use($product, $data) {
            $updated = $product->update([
                'name' => $data['name'],
                'price'=>$data['price'],
                'user_id'=>$data['user_id']
            ]);

            throw_if(!$updated, \Exception::class, 'Failed to update post');

            if($storeIds = data_get($data, 'store_id')){
                $product->stores()->sync($storeIds);
            }
            return (new ProductResource($product))->additional(['message' => 'Updated successfully']);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return Response
     */
    public function destroy(Product $product)
    {
        return DB::transaction(function () use($product) {
            $deleted = $product->forceDelete();

            throw_if(!$deleted, \Exception::class, "Cannot delete product.");

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
        $products = Product::query()->where('name', 'like', '%'.$data['name'].'%')
            ->paginate(2);

        return response()->json($products, 200);
    }
}
