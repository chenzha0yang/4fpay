const baseWebpackConfig = require('./webpack.base.conf');
const webpack = require('webpack');
const path = require('path');
const webpackMerge = require('webpack-merge');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

if (process.env.NODE_ENV === 'production') {
    module.exports = webpackMerge(baseWebpackConfig, {
        mode: 'production',
        optimization: {
            minimize: true,
            splitChunks: {
                minSize: 0,
                minChunks: 1,
                maxAsyncRequests: 50,
                maxInitialRequests: 30,
                name: true,
                cacheGroups: {
                    vendors: {
                        test: /[\\/]node_modules[\\/]/,
                        priority: -1,
                        chunks: 'all',
                        name: 'vendors'
                    },
                    assets: {
                        test: path.resolve(__dirname, './src/assets'),
                        priority: -10,
                        chunks: 'all',
                        name: 'assets'
                    }
                }
            }
        },
        plugins: [
            new CleanWebpackPlugin(['dist']),
            new OptimizeCssAssetsPlugin(),
            new MiniCssExtractPlugin({
                filename: 'css/[name].[contenthash:5].min.css',
                chunkFilename: 'css/[name].chunk.[contenthash:5].min.css'
            })
        ]
    });
} else if (process.env.NODE_ENV === 'development') {
    module.exports = webpackMerge(baseWebpackConfig, {
        mode: 'development',
        plugins: [
            new webpack.HotModuleReplacementPlugin(),
            new webpack.NoEmitOnErrorsPlugin(),
            new webpack.NamedModulesPlugin()
        ],
        devtool: 'eval-source-map',
        devServer: {
            inline: true,
            open: true, // 自动打开浏览器
            contentBase: path.join(__dirname, 'dist'),
            historyApiFallback: true,
            publicPath: '',
            compress: true,
            hot: true,
            port: 9100,
            overlay: {
                warnings: false,
                errors: true
            }
        }
    });
}
