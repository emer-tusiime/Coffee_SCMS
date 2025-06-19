<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('supplier.dashboard') }}">
            <i class="fa-solid fa-mug-hot"></i> Coffee Supplier
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#supplierNavbar" aria-controls="supplierNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="supplierNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('supplier.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('supplier.orders.index') }}">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('supplier.products.index') }}">Products</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('supplier.profile') }}">Profile</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>