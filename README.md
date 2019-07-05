# PHP Class to convert array to string

Why is it needed
---

To save your array data to file!

Situation
---

You have an array:
````php
<?php
    $arrayVar = [
        'a' => 1,
        'b' => [
            'var1',
            'var2'
        ]
    ];
````

You need a data file config.php with:

````php
<?php
    return [
        'a' => 1,
        'b' => [
            'var1',
            'var2'
        ]
    ];
````    
    
Solution:

````php
    $str = CDesign\ArrayToString\Converter($arrayVar);
    file_put_contents('config.php', "<?php return $str;");
````

Additional arguments:

    indent - tab symbol or space (default Tab symbol)
    inline - place data at one line (default false)
    shortSyntax - use `[` and `]` instead  `array(` and `)` (default true)