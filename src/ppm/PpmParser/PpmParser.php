<?php

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Declaration.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Class_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'FunctionLike.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Function_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Interface_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Method.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Namespace_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Param.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Property.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Trait_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'TraitUse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'TraitUseAdaptation.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Builder' . DIRECTORY_SEPARATOR . 'Use_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'BuilderFactory.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'BuilderHelpers.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Comment.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Comment' . DIRECTORY_SEPARATOR . 'Doc.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ConstExprEvaluationException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ConstExprEvaluator.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Error.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ErrorHandler.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ErrorHandler' . DIRECTORY_SEPARATOR . 'Collecting.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ErrorHandler' . DIRECTORY_SEPARATOR . 'Throwing.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Internal' . DIRECTORY_SEPARATOR . 'DiffElem.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Internal' . DIRECTORY_SEPARATOR . 'Differ.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeAbstract.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Internal' . DIRECTORY_SEPARATOR . 'PrintableNewAnonClassNode.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Internal' . DIRECTORY_SEPARATOR . 'TokenStream.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'JsonDecoder.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Lexer.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Lexer' . DIRECTORY_SEPARATOR . 'Emulative.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Lexer' . DIRECTORY_SEPARATOR . 'TokenEmulator' . DIRECTORY_SEPARATOR . 'TokenEmulatorInterface.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Lexer' . DIRECTORY_SEPARATOR . 'TokenEmulator' . DIRECTORY_SEPARATOR . 'CoaleseEqualTokenEmulator.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Lexer' . DIRECTORY_SEPARATOR . 'TokenEmulator' . DIRECTORY_SEPARATOR . 'FnTokenEmulator.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Lexer' . DIRECTORY_SEPARATOR . 'TokenEmulator' . DIRECTORY_SEPARATOR . 'NumericLiteralSeparatorEmulator.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NameContext.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Arg.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Const_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Array_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ArrayDimFetch.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ArrayItem.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'FunctionLike.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ArrowFunction.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Assign.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'BitwiseAnd.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'BitwiseOr.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'BitwiseXor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Coalesce.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Concat.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Div.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Minus.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Mod.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Mul.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Plus.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'Pow.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'ShiftLeft.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignOp' . DIRECTORY_SEPARATOR . 'ShiftRight.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'AssignRef.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'BitwiseAnd.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'BitwiseOr.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'BitwiseXor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'BooleanAnd.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'BooleanOr.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Coalesce.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Concat.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Div.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Equal.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Greater.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'GreaterOrEqual.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Identical.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'LogicalAnd.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'LogicalOr.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'LogicalXor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Minus.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Mod.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Mul.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'NotEqual.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'NotIdentical.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Plus.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Pow.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'ShiftLeft.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'ShiftRight.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Smaller.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'SmallerOrEqual.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BinaryOp' . DIRECTORY_SEPARATOR . 'Spaceship.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BitwiseNot.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'BooleanNot.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast' . DIRECTORY_SEPARATOR . 'Array_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast' . DIRECTORY_SEPARATOR . 'Bool_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast' . DIRECTORY_SEPARATOR . 'Double.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast' . DIRECTORY_SEPARATOR . 'Int_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast' . DIRECTORY_SEPARATOR . 'Object_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast' . DIRECTORY_SEPARATOR . 'String_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Cast' . DIRECTORY_SEPARATOR . 'Unset_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ClassConstFetch.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Clone_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Closure.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ClosureUse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ConstFetch.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Empty_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Error.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ErrorSuppress.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Eval_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Exit_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'FuncCall.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Include_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Instanceof_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Isset_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'List_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'MethodCall.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'New_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'PostDec.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'PostInc.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'PreDec.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'PreInc.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Print_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'PropertyFetch.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'ShellExec.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'StaticCall.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'StaticPropertyFetch.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Ternary.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'UnaryMinus.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'UnaryPlus.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Variable.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'Yield_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Expr' . DIRECTORY_SEPARATOR . 'YieldFrom.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Identifier.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Name.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Name' . DIRECTORY_SEPARATOR . 'FullyQualified.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Name' . DIRECTORY_SEPARATOR . 'Relative.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'NullableType.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Param.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'DNumber.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'Encapsed.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'EncapsedStringPart.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'LNumber.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'Class_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'Dir.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'File.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'Function_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'Line.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'Method.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'Namespace_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'MagicConst' . DIRECTORY_SEPARATOR . 'Trait_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Scalar' . DIRECTORY_SEPARATOR . 'String_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Break_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Case_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Catch_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'ClassLike.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Class_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'ClassConst.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'ClassMethod.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Const_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Continue_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Declare_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'DeclareDeclare.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Do_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Echo_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Else_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'ElseIf_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Expression.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Finally_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'For_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Foreach_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Function_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Global_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Goto_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'GroupUse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'HaltCompiler.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'If_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'InlineHTML.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Interface_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Label.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Namespace_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Nop.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Property.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'PropertyProperty.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Return_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Static_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'StaticVar.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Switch_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Throw_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Trait_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'TraitUse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'TraitUseAdaptation.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'TraitUseAdaptation' . DIRECTORY_SEPARATOR . 'Alias.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'TraitUseAdaptation' . DIRECTORY_SEPARATOR . 'Precedence.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'TryCatch.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Unset_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'Use_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'UseUse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'Stmt' . DIRECTORY_SEPARATOR . 'While_.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'UnionType.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'VarLikeIdentifier.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeDumper.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeFinder.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeTraverserInterface.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeTraverser.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeVisitor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeVisitorAbstract.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeVisitor' . DIRECTORY_SEPARATOR . 'CloningVisitor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeVisitor' . DIRECTORY_SEPARATOR . 'FindingVisitor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeVisitor' . DIRECTORY_SEPARATOR . 'FirstFindingVisitor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'NodeVisitor' . DIRECTORY_SEPARATOR . 'NameResolver.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Parser.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Parser' . DIRECTORY_SEPARATOR . 'Multiple.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ParserAbstract.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Parser' . DIRECTORY_SEPARATOR . 'Php5.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Parser' . DIRECTORY_SEPARATOR . 'Php7.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Parser' . DIRECTORY_SEPARATOR . 'Tokens.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ParserFactory.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'PrettyPrinterAbstract.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'PrettyPrinter' . DIRECTORY_SEPARATOR . 'Standard.php');