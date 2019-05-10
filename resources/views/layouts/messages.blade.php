@if(isset($message) and trim($message)!='')
    <article class="card-panel light-green darken-1 white-text">
        <div>{{ $message }}</div>
    </article>
@endif