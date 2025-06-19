<aside class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fa fa-users"></i> Users</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}"><i class="fa fa-shopping-cart"></i> Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}"><i class="fa fa-box"></i> Products</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.index') }}"><i class="fa fa-chart-bar"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}"><i class="fa fa-cogs"></i> Settings</a></li>
        </ul>
    </div>
</aside>