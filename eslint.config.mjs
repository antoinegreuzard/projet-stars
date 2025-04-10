import { defineConfig } from 'eslint/config'
import { fixupConfigRules, fixupPluginRules } from '@eslint/compat'
import vue from 'eslint-plugin-vue'
import typescriptEslint from '@typescript-eslint/eslint-plugin'
import globals from 'globals'
import parser from 'vue-eslint-parser'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import js from '@eslint/js'
import { FlatCompat } from '@eslint/eslintrc'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all
})

export default defineConfig([
    {
        extends: fixupConfigRules(
            compat.extends(
                'plugin:vue/recommended',
                'eslint:recommended',
                'plugin:@typescript-eslint/recommended',
                'plugin:import/recommended',
                'plugin:prettier/recommended'
            )
        ),

        plugins: {
            vue: fixupPluginRules(vue),
            '@typescript-eslint': fixupPluginRules(typescriptEslint)
        },

        languageOptions: {
            globals: {
                ...globals.browser,
                route: 'readonly'
            },

            parser,
            ecmaVersion: 'latest',
            sourceType: 'module',

            parserOptions: {
                parser: '@typescript-eslint/parser',

                ecmaFeatures: {
                    jsx: true
                }
            }
        },

        settings: {
            'import/resolver': {
                node: {
                    extensions: ['.js', '.ts', '.json']
                },

                typescript: {},

                alias: {
                    map: [['@', './resources/js']],
                    extensions: ['.js', '.ts', '.vue']
                }
            }
        },

        rules: {
            'vue/multi-word-component-names': 'off',

            'import/extensions': [
                'error',
                'ignorePackages',
                {
                    ts: 'never',
                    js: 'never'
                }
            ],

            'vue/no-multiple-template-root': 'off'
        }
    }
])
