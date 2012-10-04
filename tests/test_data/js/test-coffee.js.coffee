# Array Literals
# --------------

# * Array Literals
# * Splats in Array Literals

# TODO: add indexing and method invocation tests: [1][0] is 1, [].toString()

test "trailing commas", ->
  trailingComma = [1, 2, 3,]
  ok (trailingComma[0] is 1) and (trailingComma[2] is 3) and (trailingComma.length is 3)

  trailingComma = [
    1, 2, 3,
    4, 5, 6
    7, 8, 9,
  ]
  (sum = (sum or 0) + n) for n in trailingComma