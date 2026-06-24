@extends('layouts.app')

@section('title', 'Add Package Type')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add Package Type</h5>
                    <small class="text-muted float-end">Lengkapi data berikut</small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.packageType.store') }}">
                        @csrf
                        {{-- Nama Package Type --}}
                        <div class="mb-3">
                            <label class="form-label" for="packageType">Package Type</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-star"></i></span>
                                <input type="text" id="packageType" name="packageType"
                                    class="form-control @error('packageType') is-invalid @enderror"
                                    placeholder="Contoh: Premium" value="{{ old('packageType') }}" required>
                                @error('packageType')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Perlu Konsultasi Ahli Gizi --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Perlu Konsultasi Ahli Gizi?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="is_personal" id="is_personal_yes" value="1"
                                           {{ old('is_personal', '0') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_personal_yes">
                                        <span class="badge bg-label-primary me-1"><i class="bx bx-user-check"></i></span>
                                        Ya — order masuk ke Ahli Gizi
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="is_personal" id="is_personal_no" value="0"
                                           {{ old('is_personal', '0') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_personal_no">
                                        <span class="badge bg-label-secondary me-1"><i class="bx bx-x"></i></span>
                                        Tidak — order reguler biasa
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                Jika <strong>Ya</strong>, semua order dengan tipe paket ini akan tampil di dashboard Ahli Gizi untuk dikonsultasikan.
                            </small>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.packageType') }}" class="btn btn-outline-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
