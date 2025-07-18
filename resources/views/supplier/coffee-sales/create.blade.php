@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Report Coffee Sale</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier.coffee-sales.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">Quantity (kg)</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" step="0.01" min="0" required
                                           value="{{ old('quantity') }}">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_per_kg">Price per kg</label>
                                    <input type="number" class="form-control @error('price_per_kg') is-invalid @enderror" id="price_per_kg" name="price_per_kg" step="0.01" min="0" required
                                           value="{{ old('price_per_kg') }}">
                                    @error('price_per_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quality_grade">Quality Grade</label>
                                    <select class="form-control @error('quality_grade') is-invalid @enderror" id="quality_grade" name="quality_grade" required>
                                        <option value="" disabled {{ old('quality_grade') ? '' : 'selected' }}>Select Quality Grade</option>
                                        <option value="AA" {{ old('quality_grade') === 'AA' ? 'selected' : '' }}>AA</option>
                                        <option value="A" {{ old('quality_grade') === 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('quality_grade') === 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('quality_grade') === 'C' ? 'selected' : '' }}>C</option>
                                        <option value="Other" {{ old('quality_grade') === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('quality_grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="notes">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Report Sale
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
