@if(count($errors))
    <div class="col m12">
        <article class="card-panel red lighten-1 white-text">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </article>
    </div>
@endif