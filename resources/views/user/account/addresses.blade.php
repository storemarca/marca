@extends('layouts.user')

@section('title', __('addresses'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">{{ __('addresses') }}</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('your_addresses') }}</h2>
                    <button type="button" id="add-address-btn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('add_new_address') }}
                    </button>
                </div>

                @if($addresses->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('no_addresses') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('add_address_to_start') }}</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($addresses as $address)
                            <div class="border rounded-md p-4 relative {{ $address->is_default ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200' }}">
                                @if($address->is_default)
                                    <span class="absolute top-2 right-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ __('default') }}
                                    </span>
                                @endif
                                
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $address->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $address->phone }}</p>
                                </div>
                                
                                <div class="text-sm text-gray-500">
                                    <p>{{ $address->address_line1 }}</p>
                                    @if($address->address_line2)
                                        <p>{{ $address->address_line2 }}</p>
                                    @endif
                                    <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                    <p>{{ $address->country->name }}</p>
                                </div>
                                
                                <div class="mt-4 flex space-x-2 rtl:space-x-reverse">
                                    <button type="button" class="edit-address-btn inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                        data-address="{{ json_encode($address) }}">
                                        {{ __('edit') }}
                                    </button>
                                    
                                    @if(!$address->is_default)
                                        <form action="{{ route('user.account.addresses.destroy', $address->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                onclick="return confirm('{{ __('confirm_delete_address') }}')">
                                                {{ __('delete') }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('user.account.addresses.update', $address->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="name" value="{{ $address->name }}">
                                            <input type="hidden" name="phone" value="{{ $address->phone }}">
                                            <input type="hidden" name="address_line1" value="{{ $address->address_line1 }}">
                                            <input type="hidden" name="address_line2" value="{{ $address->address_line2 }}">
                                            <input type="hidden" name="city" value="{{ $address->city }}">
                                            <input type="hidden" name="state" value="{{ $address->state }}">
                                            <input type="hidden" name="postal_code" value="{{ $address->postal_code }}">
                                            <input type="hidden" name="country_id" value="{{ $address->country_id }}">
                                            <input type="hidden" name="is_default" value="1">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                {{ __('set_as_default') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div id="address-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    {{ __('add_new_address') }}
                </h3>
                <div class="mt-4">
                    <form id="address-form" method="POST" action="{{ route('user.account.addresses.store') }}">
                        @csrf
                        <input type="hidden" name="_method" id="form-method" value="POST">
                        <input type="hidden" name="address_id" id="address-id" value="">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('name') }}</label>
                                <input type="text" name="name" id="address-name" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-1">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('phone') }}</label>
                                <input type="text" name="phone" id="address-phone" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-2">
                                <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-1">{{ __('address_line1') }}</label>
                                <input type="text" name="address_line1" id="address-line1" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-2">
                                <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-1">{{ __('address_line2') }} ({{ __('optional') }})</label>
                                <input type="text" name="address_line2" id="address-line2"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-1">
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">{{ __('city') }}</label>
                                <input type="text" name="city" id="address-city" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-1">
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">{{ __('state') }}</label>
                                <input type="text" name="state" id="address-state" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-1">
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('postal_code') }}</label>
                                <input type="text" name="postal_code" id="address-postal-code" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-1">
                                <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('country') }}</label>
                                <select name="country_id" id="address-country" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                                    <option value="">{{ __('select_country') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_default" id="address-default" value="1"
                                        class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                    <label for="is_default" class="ml-2 block text-sm text-gray-900">
                                        {{ __('set_as_default_address') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="save-address-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('save') }}
                </button>
                <button type="button" id="cancel-address-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('address-modal');
        const addAddressBtn = document.getElementById('add-address-btn');
        const cancelAddressBtn = document.getElementById('cancel-address-btn');
        const saveAddressBtn = document.getElementById('save-address-btn');
        const addressForm = document.getElementById('address-form');
        const editAddressBtns = document.querySelectorAll('.edit-address-btn');
        const modalTitle = document.getElementById('modal-title');
        const formMethod = document.getElementById('form-method');
        
        // Show modal on add address button click
        addAddressBtn.addEventListener('click', function() {
            modalTitle.textContent = "{{ __('add_new_address') }}";
            addressForm.reset();
            addressForm.action = "{{ route('user.account.addresses.store') }}";
            formMethod.value = 'POST';
            document.getElementById('address-id').value = '';
            modal.classList.remove('hidden');
        });
        
        // Hide modal on cancel button click
        cancelAddressBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        // Submit form on save button click
        saveAddressBtn.addEventListener('click', function() {
            addressForm.submit();
        });
        
        // Edit address
        editAddressBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const addressData = JSON.parse(this.dataset.address);
                modalTitle.textContent = "{{ __('edit_address') }}";
                
                document.getElementById('address-name').value = addressData.name;
                document.getElementById('address-phone').value = addressData.phone;
                document.getElementById('address-line1').value = addressData.address_line1;
                document.getElementById('address-line2').value = addressData.address_line2 || '';
                document.getElementById('address-city').value = addressData.city;
                document.getElementById('address-state').value = addressData.state;
                document.getElementById('address-postal-code').value = addressData.postal_code;
                document.getElementById('address-country').value = addressData.country_id;
                document.getElementById('address-default').checked = addressData.is_default;
                
                addressForm.action = "{{ route('user.account.addresses.store') }}".replace('store', addressData.id);
                formMethod.value = 'PATCH';
                document.getElementById('address-id').value = addressData.id;
                
                modal.classList.remove('hidden');
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
@endpush 