# API Response Pattern (HATEOAS)

All responses must follow:

- status
- message
- errors
- data
- _links

Example:

return response()->json([
    'status' => true,
    'message' => '',
    'errors' => [],
    'data' => $data,
    '_links' => [
        'self' => [
            'href' => '',
            'method' => ''
        ],
		'create': {
			'href': '',
			'method': 'POST'
		},
		'delete': {
			'href': '',
			'method': 'DELETE'
		},
		'update': {
			'href': '',
			'method': 'PUT'
		}
    ]
]);
