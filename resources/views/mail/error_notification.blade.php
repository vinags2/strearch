<div>
	<h2>Error Notification</h2>
	<br><br>
	<div>{!! $error_message !!}</div>
	<br><br>
	<div>
		Date: {{ now()->format('Y-m-d') }}<br>
		Time: {{ now()->format('H:i:s') }}<br>
		User: {{ auth()->user() ? auth()->user()->name : 'Guest' }}<br>
		IP Address: {{ request()->ip() }}<br>
	</div>
	<div>
		@if($error_code)
			Error Code: {!! $error_code !!}<br>
		@endif
		@if($error_response)
			Error Response: {!! $error_response !!}<br>
		@endif
		Flag: {!! $flag !!}<br>
	</div>
	<br><br>
	<i>
		This is a system-generated email. Do not reply to this email.
	</i>
	</p>
</div>