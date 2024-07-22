const path = require('path');
const NodePolyfillPlugin = require('node-polyfill-webpack-plugin');

module.exports = {
    entry: {
        modelEngine: './js/modelEngine.js',
        vectorDB: './js/vectorDB.js',
        chatbotFrontend: './js/chatbotFrontend.js',
    },
    plugins: [
        new NodePolyfillPlugin()
    ],
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, 'public/js'),
    },
    externals: {
        jquery: 'jQuery',
    },
    resolve: {
        alias: {
            VAFWpFramework: path.resolve(__dirname, 'vendor/vonaffenfels/vaf-wp-framework/js'),
            css: path.resolve(__dirname, 'css')
        },
        fallback: {
            "fs": false
        },
    },
    optimization: {
        minimize: true
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
                options: {
                    presets: [
                        ['@babel/preset-env']
                    ]
                }
            },
            {
                test: /\.css$/,
                use: [
                    'style-loader',
                    'css-loader'
                ]
            },
            {
                test: /\.scss$/,
                use: [
                    'style-loader',
                    'css-loader',
                    'sass-loader'
                ]
            }
        ]
    },
};
