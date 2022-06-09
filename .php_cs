<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setRules([
        '@PSR2'                                      => true, //基準となるルールの設定
        'single_import_per_statement'                => false, // マルチインポート use HOGE\{A,B}; を展開するのを禁止
        'array_syntax'                               => ['syntax' => 'short'], //配列の書き方について
        'global_namespace_import'                    => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],  //namespaceの書き方について
        'concat_space'                               => ['spacing' => 'one'], //文字列連結の際のスペースについて
        'no_unused_imports'                          => true, // useで使われていないクラスを許可しない
        'ordered_imports'                            => true, // useをソートする
        'declare_strict_types'                       => true, // declare(strict_types=1);を付ける
        'binary_operator_spaces'                     => [ // =>, = の位置を揃える
            'align_double_arrow' => true,
            'align_equals'       => true,
        ],
        'blank_line_before_return'                   => true, // return前に空行を入れる
        'function_typehint_space'                    => true, // タイプヒンティングの後に空白を入れる
        'method_separation'                          => true, // メソッドの間に空行を入れる
        'return_type_declaration'                    => true, // リターンタイプのセミコロンに空白を入れる
        'no_whitespace_in_blank_line'                => true, // <?php後の空行を消す
        'hash_to_slash_comment'                      => true, // #タイプのコメントを除外する
        'lowercase_cast'                             => true, // キャスト演算子を小文字に強制する
        'lowercase_constants'                        => true, // boolやnullを小文字に強制する
        'native_function_casing'                     => true, // ビルトイン関数を小文字に強制する
        'no_blank_lines_after_class_opening'         => true, // クラスの最初のメソッドまでの空行を除去する
        'no_blank_lines_after_phpdoc'                => true, // クラスのDocBlockの空行を除去する
        'no_blank_lines_before_namespace'            => true, // 名前空間までの空行を除去する
        'no_empty_comment'                           => true, // 空コメントを除去する
        'no_empty_phpdoc'                            => true, // 空コメントを除去する
        'no_empty_statement'                         => true, // 空の終端文字;を除去する
        'no_leading_import_slash'                    => true, // useの無駄な先頭バックスラッシュを除去する
        'no_leading_namespace_whitespace'            => true, // useの無駄な空白を除去する
        'no_short_bool_cast'                         => true, // 変則boolキャスト(!!$a)を(bool)$aにする
        'no_singleline_whitespace_before_semicolons' => true, // 終端文字;前の空白を除去する
        'no_spaces_around_offset'                    => true, // 変数指定ブラケット内$a[]の中の空白を除去する
        'no_unneeded_control_parentheses'            => true, // print()等の無駄括弧を除去する
        'no_whitespace_before_comma_in_array'        => true, // 配列のカンマ位置を前揃えにする
        'normalize_index_brace'                      => true, // 配列へのアクセスを[]ブラケットで統一する
        'object_operator_without_whitespace'         => true, // オブジェクトへの->アクセスの空白を除去する
        'phpdoc_align'                               => true, // docblock内の@コメントを左寄せにする
        'phpdoc_scalar'                              => true, // docblock内の@コメントのスカラ値表記を統一する
        'phpdoc_separation'                          => true, // docblock内の@コメントをグループごとにセパレーションする
        'phpdoc_trim'                                => true, // docblock内の無駄行を除去する
        'phpdoc_types'                               => true, // docblock内の@コメントのスカラタイプ表記を統一する
        'phpdoc_var_without_name'                    => true, // プロパティの@varの変数名を除去する
        'self_accessor'                              => true, // クラス内の静的クラス名アクセスをselfにする
        'standardize_not_equals'                     => true, // not equal演算子を<>から!=に統一する
        'ternary_operator_spaces'                    => true, // 三項演算子を各スペースが入るように統一する
        'trailing_comma_in_multiline_array'          => true, // 配列の最後の要素のカンマを強制する
        'trim_array_spaces'                          => true, // 配列の無駄空白を除去する
        'unary_operator_spaces'                      => true, // 演算子の無駄な空白を除去する
        'whitespace_after_comma_in_array'            => true, // 詰めすぎている配列を左詰めスペースに統一する
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__)
    );
