@extends('layouts.app')

@section('title','Add Brand')
@section('menuSuperadminBrand','bg-primary text-white active')
<!-- @livewire('sobat.superadmin.user.index') -->
 @section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>
        @yield('title')
    </h1>

    <div class="card">
        <div class="card-header bg-primary">
            <div class="d-flex justify-content-between">
                <div class="mb-1 mr-2">
                    <a href="{{ route('brand') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('brandStore') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12 mb-2">
                        <label class="form-label">
                            <span class="text-danger">
                                *
                            </span>
                            Brand Name :
                        </label>
                        <input class="form-control @error('brandname') is-invalid @enderror" type="text" name="brandname" value="{{ old('brandname') }}"></input>
                        @error('brandname')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">
                            Description :
                        </label>
                        <textarea class="form-control @error('branddescription') is-invalid @enderror" id="content" name="branddescription">{{ old('branddescription') }}</textarea>
                        @error('branddescription')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">
                            Status :
                        </label>
                        <select class="form-control @error('brandstatus') is-invalid @enderror" name="brandstatus">
                            <option selected disabled>-- Choose --</option>
                            <option value="Active">Active</option>
                            <option value="InActive">InActive</option>
                        </select>
                        @error('brandstatus')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">
                            Upload Photo (200x200px, max 20KB):
                        </label>
                        <input type="file" class="form-control @error('brandphoto') is-invalid @enderror" name="brandphoto" accept="image/*">
                        @error('brandphoto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection