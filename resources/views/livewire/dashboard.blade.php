<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-card bg-accent p-4 sm:p-8">
                <div class="max-w rounded-lg mb-6">
                    <h1 class="text-xl font-extrabold text-primary">User Dashboard</h1>
                    <h2 class="mt-4 text-2xl font-extrabold text-primary-foreground">Halo, {{ Auth::user()->name }}</h2>
                </div>
                <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2">
                    <a href="{{ route('product.index') }}" class="px-4 py-2 bg-surface rounded-lg text-content font-semibold focus:outline-none">Cek Katalog</a>
                    <a href="{{ route('orders.history') }}" class="px-4 py-2 bg-surface rounded-lg text-content font-semibold focus:outline-none">Daftar History Pembelian</a>
                </div>
            </div> 
        </div>
    </div>
</x-app-layout>
