<div>
	{!! $content !!}
	<i>
		This is a system-generated email. Do not reply to this email.
	@if($includeReturnAddress == 'true')
		Email <a href="mailto: info@u3abathurst.org.au">the Bathurst U3A Commitee</a> for questions, comments, feedback, etc.
	@endif
	</i>
	</p>
</div>