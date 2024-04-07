<?php

namespace App\Http\Controllers;

use App\Jobs\ProductLiked;
use App\Models\Product;
use App\Models\ProductUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function likes($id, Request $request)
    {
        $response = Http::get('http://localhost:8081/api/users');

        $user = $response->json();

        try {
            $product = ProductUser::create([
                'user_id' => $user['id'],
                'product_id' => $id
            ]);

            ProductLiked::dispatch($product->toArray())->onQueue('admin_queue');

            return response([
                'message' => 'success'
            ]);
        } catch (\Exception $exception) {
            return response([
                'error' => 'You already liked this product !'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
