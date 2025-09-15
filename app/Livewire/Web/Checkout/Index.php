<?php

namespace App\Livewire\Web\Checkout;

use App\Models\Cart;
use Livewire\Component;
use App\Models\Province;
use Illuminate\Support\Facades\Http;

class Index extends Component
{
    public $address;
    public $province_id;
    public $city_id;
    public $district_id;

    public $loading  = false;
    public $showCost = false;
    public $costs;

    public $selectCourier = '';
    public $selectService = '';
    public $selectCost = 0;

    public $grandTotal = 0;

    /**
     * getCartsData
     *
     * @return void
     */
    public function getCartsData()
    {
        //get carts by customer
        $carts = Cart::query()
            ->with('product')
            ->where('customer_id', auth()->guard('customer')->user()->id)
            ->latest()
            ->get();

        // Menghitung total berat
        $totalWeight = $carts->sum(function ($cart) {
            return $cart->product->weight * $cart->qty;
        });

        // Menghitung total harga
        $totalPrice = $carts->sum(function ($cart) {
            return $cart->product->price * $cart->qty;
        });

        // Return as an array
        return [
            'totalWeight' => $totalWeight,
            'totalPrice'  => $totalPrice,
        ];
    }

    /**
     * changeCourier
     *
     * @param  mixed $value
     * @return void
     */
    public function changeCourier($value)
    {
        if (!empty($value)) {

            //set courier
            $this->selectCourier = $value;

            //set loading
            $this->loading = true;

            //set show cost false
            $this->showCost = false;

            //call method CheckOngkir
            $this->CheckOngkir();
        }
    }

    /**
     * Ongkir function: calculate shipping cost or any logic.
     *
     * @return void
     */
    public function CheckOngkir()
    {
        try {
            $cartData = $this->getCartsData();

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'key'    => config('rajaongkir.api_key'),
            ])->withOptions([
                'query' => [
                    'origin'      => 3855,
                    'destination' => $this->district_id,
                    'weight'      => $cartData['totalWeight'],
                    'courier'     => $this->selectCourier,
                ]
            ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost');

            $this->costs = $response->json()['data'];

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengambil ongkir: ' . $e->getMessage());
        } finally {
            $this->loading = false;
            $this->showCost = true;
        }
    }

    /**
     * getServiceAndCost
     *
     * @param  mixed $data
     * @return void
     */
    public function getServiceAndCost($data)
    {
        // Pecah data menjadi nilai cost dan service
        [$cost, $service] = explode('|', $data);

        // Set nilai cost dan service
        $this->selectCost = (int) $cost;
        $this->selectService = $service;

        // Ambil total harga dari cart
        $cartData = $this->getCartsData();

        // Hitung grand total
        $this->grandTotal = $cartData['totalPrice'] + $this->selectCost;
    }

    public function render()
    {
        //get provinces
        $provinces = Province::query()->get();

        //get total cart price
        $cartData = $this->getCartsData();
        $totalPrice     = $cartData['totalPrice'];
        $totalWeight    = $cartData['totalWeight'];

        // Kirim ke view Livewire
        return view('livewire.web.checkout.index', compact('provinces', 'totalPrice', 'totalWeight'));
    }
}