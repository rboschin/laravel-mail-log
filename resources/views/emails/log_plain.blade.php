{{$payload['datetime'] ?? ''}} - {{$payload['level'] ?? ''}} - {{$payload['message'] ?? ''}}

@if(!empty($payload['request']))
Request: {{$payload['request']['method'] ?? ''}} {{$payload['request']['path'] ?? ''}} (IP: {{$payload['request']['ip'] ?? ''}})
@endif

Environment: {{$payload['app_env'] ?? ''}}
Host: {{$payload['hostname'] ?? ''}}

@if(!empty($payload['context']))
Context:
@json($payload['context'])
@endif

@if(!empty($payload['extra']))
Extra:
@json($payload['extra'])
@endif

@if(!empty($verbose))
Full payload:
@json($payload)
@endif
