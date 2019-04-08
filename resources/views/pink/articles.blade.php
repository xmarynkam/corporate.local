@extends(config('settings.theme').'.layouts.site')

@section('navigation')
    {!! $navigation !!}
@endsection

@section('content')
	@if(isset($content))
    	{!! $content !!}
    @else
    	<div id="content-blog" class="content group">
    		<h2 class="post-title">Дана стаття не існує</h2>
    	</div>
    @endif
@endsection

@section('bar')
    {!! $rightBar !!}
@endsection

@section('footer')
    {!! $footer !!}
@endsection