<?php // lint >= 7.3

[1, 2, 3];

[
	1,
	2,
	[
		3,
		5,
	],
	5
];

[
	1,
	2,
	3
];

[
	1,
	2,
	[
		3,
		5
	],
	5,
];

[
	//
];

[
	1,
	//
	3
];

[
	<<<'EOF'
anything
EOF
];

[
	<<<"EOF"
anything
EOF
];

[
	<<<'EOF'
anything
EOF
];
