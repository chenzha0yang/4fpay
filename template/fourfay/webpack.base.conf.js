const views = require('./src/views.json');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin')
const isProd = (process.env.NODE_ENV === 'production');
const plugins = getViews(views);

function getViews(views) {

    const v = []

    views.forEach(page => {

        const chunks = [page.url];
        if (isProd) {
            chunks.splice(0, 0, 'assets');
            chunks.splice(0, 0, 'vendors');
        }

        v.push(
            new HtmlWebpackPlugin({
                filename: path.resolve(__dirname, `./dist/${page.url}.html`),
                template: path.resolve(__dirname, `./src/${page.url}.html`),
                chunks: page.isUrl ? chunks : [],
                chunksSortMode: 'manual',
                minify: isProd ? {
                    collapseWhitespace: true,
                    removeComments: true
                } : false
            })
        );

    })

    return v
}

// if (isProd) {
//     plugins.push(
//         new MiniCssExtractPlugin({
//             filename: 'css/[name].[contenthash:5].min.css',
//             chunkFilename: 'css/[name].chunk.[contenthash:5].min.css'
//         })
//     );
// }

module.exports = {
    entry: {
        index: path.resolve(__dirname, `./src/assets/js/index.js`)
    },
    output: {
        publicPath: isProd ? './' : '',
        path: path.resolve(__dirname, './dist'),
        filename: 'js/' + (isProd ? '[name].[chunkhash:5].js' : '[name].js'),
        chunkFilename: 'js/' + (isProd ? '[name].chunk.[chunkhash:5].js' : '[name].chunk.js'),
    },
    module: {
        rules: [{
                test: require.resolve('jquery'),
                use: [{
                    loader: 'expose-loader',
                    options: 'jQuery'
                }, {
                    loader: 'expose-loader',
                    options: '$'
                }]
            },
            {
                test: /\.(html|htm)$/,
                use: ['html-loader']
            },
            {
                test: /\.(png|jpg|jpe?g|gif)$/,
                // use: [{
                //     loader: "url-loader",
                //     options: {
                //         limit: 40,
                //         name: (isProd ? '[name].[hash:5]' : '[name]') + '.[ext]',
                //         outputPath: "static/images/",
                //         publicPath: '../images'
                //     }
                // }, 'image-webpack-loader']
                use: ['url-loader?limit=40&name=[name]' + (isProd ? '.[hash:5]' : '') + '.[ext]&outputPath=images/', 'image-webpack-loader']
            },
            // {
            //     test: /\.(webp)$/,
            //     use: ['file-loader?&name=[name]' + (isProd ? '.[hash:5]' : '') + '.[ext]&outputPath=img/']
            // },
            {
                test: /\.(svg|woff|woff2|ttf|eot)(\?.*$|$)/,
                loader: 'file-loader?name=font/[name].[hash:5].[ext]'
            },
            {
                test: /\.(css)$/,
                use: [isProd ? ({
                    loader: MiniCssExtractPlugin.loader,
                    options: {
                        publicPath: '../'
                    }
                }) : 'style-loader', 'css-loader']
            },
            {
                test: /\.(scss)$/,
                use: [isProd ? ({
                    loader: MiniCssExtractPlugin.loader,
                    options: {
                        publicPath: '../'
                    }
                }) : 'style-loader', 'css-loader', {
                    loader: 'postcss-loader',
                    options: {
                        plugins: function () {
                            return [
                                require('autoprefixer')
                            ];
                        }
                    }
                }, 'sass-loader']
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['es2015-nostrict'],
                        plugins: ['transform-runtime']
                    }
                }
            }
        ]
    },
    plugins: [
        new CopyWebpackPlugin([{
            from: path.resolve(__dirname, './static'),
            to: 'static',
            ignore: ['.*']
        }]),
        ...plugins
    ]
};
