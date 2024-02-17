
@include('header')

<body>
  <div class="wrapper">
    <section class="users">
        <header>
            <div class="content">
                <img src="/img/{{ $img }}" alt="">
                <div class="details">
                    <span>{{ $fname }} {{ $lname }}</span>
                    <p>{{ $status }} </p>
                </div>
            </div>
            <a href="/logout?logout_id={{ $uniqueId }}" class="logout">Logout</a>
        </header>
      </div>
    </section>
  </div>
  
</body>
</html>
