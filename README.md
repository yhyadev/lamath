# LaMath

A math interpreter written in [Syphon](https://github.com/syphon-lang/syphon), [previously](https://github.com/yhyadev/lamath-php) was in PHP

## Quick Try

```console
$ syphon src/main.sy
>> 5 + 5
10
```

## Syntax

- The numbers are reperesentated as Syphon floats
- A math expression is a step, and the whole line of expressions is a procedure
- Steps are separated by space

The available unary operators are:
- [-]

- -: Negation

The available binary operators are:
- [+, -, /, *, **]
- +: Addition
- -: Subtraction
- /: Division
- *: Multiplication
- **: Exponentation
