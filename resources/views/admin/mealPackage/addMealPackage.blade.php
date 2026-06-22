@extends('layouts.app')

@section('title', 'Add Meal Package')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah Data Meal Package</h5>
                    <small class="text-muted float-end">Lengkapi data di bawah ini</small>
                </div>

                <div class="card-body">
                    <form id="mealPackageForm" method="POST" action="{{ route('admin.mealPackage.store') }}">
                        @csrf

                        {{-- Nama Meal Package --}}
                        <div class="mb-3">
                            <label class="form-label" for="nama_meal_package">Nama Meal Package</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-restaurant"></i></span>
                                <input type="text" id="nama_meal_package" name="nama_meal_package"
                                    class="form-control @error('nama_meal_package') is-invalid @enderror"
                                    placeholder="Contoh: Diet Booster, Healthy Fit Plan"
                                    value="{{ old('nama_meal_package') }}" />
                                @error('nama_meal_package')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        {{-- Batch --}}
                        <div class="mb-3">
                            <label class="form-label" for="batch">Batch</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-package"></i></span>
                                <input type="text" id="batch" name="batch"
                                    class="form-control @error('batch') is-invalid @enderror"
                                    placeholder="Contoh : I, II, III" value="{{ old('batch') }}" />
                                @error('batch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Package Type --}}
                        <div class="mb-3">
                            <label class="form-label" for="package_type_id">Package Type</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-category"></i></span>
                                <select id="package_type_id" name="package_type_id"
                                    class="form-select @error('package_type_id') is-invalid @enderror">
                                    <option value="">-- Pilih Package Type --</option>
                                    @foreach ($packageTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('package_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->packageType }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('package_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Jenis Paket --}}
                        @php
                            // Satu sumber: value (kunci) = yang disimpan ke DB, label = yang tampil di UI
                            $options = [
                                'harian' => 'Harian',
                                'paket mingguan' => 'Paket Mingguan',
                                'paket bulanan' => 'Paket Bulanan',
                                'paket 3 bulanan' => 'Paket 3 Bulanan',
                            ];
                            // Ambil nilai terpilih: prioritas old() lalu fallback ke data edit ($mealPackage jika ada)
                            $selected = old('jenis_paket', $mealPackage->jenis_paket ?? '');
                        @endphp

                        <div class="mb-3">
                            <label class="form-label" for="jenis_paket">Jenis Paket</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <select id="jenis_paket" name="jenis_paket"
                                    class="form-select @error('jenis_paket') is-invalid @enderror">
                                    <option value="" disabled {{ $selected === '' ? 'selected' : '' }}>-- Pilih Jenis
                                        Paket --</option>
                                    @foreach ($options as $value => $label)
                                        <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        {{-- Porsi Paket --}}
                        {{-- Porsi Paket --}}
                        <div class="mb-3">
                            <label class="form-label" for="porsi_paket">Porsi Paket</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-bowling-ball"></i></span>
                                <select id="porsi_paket" name="porsi_paket"
                                    class="form-select @error('porsi_paket') is-invalid @enderror">
                                    <option value="">-- Pilih Porsi Paket --</option>
                                    <option value="harga per porsi"
                                        {{ old('porsi_paket') == ' harga per porsi' ? 'selected' : '' }}>
                                        harga per porsi
                                    </option>
                                    <option value="4 hari 2 kali makan (siang dan sore)"
                                        {{ old('porsi_paket') == '4 hari 2 kali makan (siang dan sore)' ? 'selected' : '' }}>
                                        4 hari 2 kali makan (siang dan sore)
                                    </option>
                                    <option value="8 hari 1 kali makan (siang/malam saja)"
                                        {{ old('porsi_paket') == '8 hari 1 kali makan (siang/malam saja)' ? 'selected' : '' }}>
                                        8 hari 1 kali makan (siang/malam saja)
                                    </option>
                                    <option value="12 hari 2 kali makan (siang dan sore)"
                                        {{ old('porsi_paket') == '12 hari 2 kali makan (siang dan sore)' ? 'selected' : '' }}>
                                        12 hari 2 kali makan (siang dan sore)
                                    </option>
                                    <option value="24 hari 1 kali makan (siang/malam saja)"
                                        {{ old('porsi_paket') == '24 hari 1 kali makan (siang/malam saja)' ? 'selected' : '' }}>
                                        24 hari 1 kali makan (siang/malam saja)
                                    </option>
                                    <option value="36 hari 2 kali makan (siang dan sore)"
                                        {{ old('porsi_paket') == '36 hari 2 kali makan (siang dan sore)' ? 'selected' : '' }}>
                                        36 hari 2 kali makan (siang dan sore)
                                    </option>
                                    <option value="72 hari 1 kali makan (siang/malam saja)"
                                        {{ old('porsi_paket') == '72 hari 1 kali makan (siang/malam saja)' ? 'selected' : '' }}>
                                        72 hari 1 kali makan (siang/malam saja)
                                    </option>
                                    <option value="2 kali makan (siang dan malam)"
                                        {{ old('porsi_paket') == '2 kali makan (siang dan malam)' ? 'selected' : '' }}>
                                        2 kali makan (siang dan malam)
                                    </option>
                                </select>
                                @error('porsi_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Total Hari --}}
                        <div class="mb-3">
                            <label class="form-label" for="total_hari">Total Hari</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-package"></i></span>
                                <input type="number" id="total_hari" name="total_hari"
                                    class="form-control @error('total_hari') is-invalid @enderror"
                                    placeholder="Contoh : 1, 2, 3" value="{{ old('total_hari') }}" />
                                @error('total_hari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Detail Paket --}}
                        <div class="mb-3">
                            <label class="form-label" for="detail_paket">Detail Paket</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-detail"></i></span>
                                <textarea id="detail_paket" name="detail_paket" rows="3"
                                    class="form-control @error('detail_paket') is-invalid @enderror" placeholder="Tuliskan detail paket">{{ old('detail_paket') }}</textarea>
                                @error('detail_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Harga --}}
                        <div class="mb-3">
                            <label class="form-label" for="price">Harga</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-money"></i></span>
                                <input type="text" id="price_display" class="form-control"
                                    placeholder="Contoh: Rp150.000"
                                    value="{{ old('price') ? 'Rp' . number_format(old('price'), 0, ',', '.') : '' }}" />
                                <input type="hidden" id="price" name="price" value="{{ old('price') }}">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            {{-- <a href="{{ route('meal-packages.index') }}" class="btn btn-outline-secondary">Batal</a> --}}
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const priceInput = document.getElementById('price');
        const priceDisplay = document.getElementById('price_display');

        priceDisplay.addEventListener('input', function() {
            // Ambil hanya angka
            let rawValue = this.value.replace(/[^\d]/g, '');
            // Simpan angka murni di input hidden
            priceInput.value = rawValue;

            // Format ke rupiah
            if (rawValue) {
                this.value = formatRupiah(rawValue);
            } else {
                this.value = '';
            }
        });

        function formatRupiah(angka) {
            return 'Rp' + angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>
@endpush
