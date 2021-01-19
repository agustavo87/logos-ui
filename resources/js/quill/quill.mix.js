const mix = require('laravel-mix');
const path = require('path');

class QuillMix {
    register(project_root_dir) {
        this.root = project_root_dir; // Necesario para resolver la ubicacion del codigo fuente y usar un alias
                                      // en vez de la versión de distribución.
        console.log("Configuración de Mix para Quill cargada correctamente.");
        console.log("Raiz:", this.root);
    }

    boot() {
        console.log("Configurando Mix para Quill.")
    }

    dependencies() {
        // Dependencias para compilar Parchment (typescript) y para cargar SVG ('html-loader')
        return ['typescript', 'ts-loader', 'html-loader']
        // Quill también usa babel, pero mix lo tendría que resolver automáticamente.
    }

    webpackRules() {
        return [{
            // Quill utiliza un cargador HTML, no un SVG como es por defecto en Mix.
            test: /(quill).*\.svg$/,
            use: [{
                loader: 'html-loader',
                options: {
                    minimize: true
                }
            }]
        }, {
            // Necesario para Parchment
            test: /\.ts$/,
            use: [{
                loader: 'ts-loader',
                options: {
                    compilerOptions: {
                        declaration: false,
                        target: 'es5',
                        module: 'commonjs'
                    },
                    transpileOnly: true
                }
            }]
        }]
    }

    webpackConfig(webpackConfig) {
        // Se excluye Quill de los cargadores de imágenes para archivos SVG.
        // 1ro. se búsca el indice del cargador de *.svg que utilza cargadores imágenes
        let svgImageLoaderIndex = webpackConfig.module.rules.findIndex(rule => {
            let index = rule.test.test('p.svg')
            if (!index) return false;
            let useIndex = rule.use.findIndex(loader => loader.loader === 'file-loader' || loader.loader === 'img-loader');
            return useIndex < 0 ? false : true;
        })

        // console.log('indice',svgImageLoaderIndex)
        // 2do. se excluye al directorio quill de dichas reglas.
        webpackConfig.module.rules[svgImageLoaderIndex].exclude = /(quill).*\.svg$/;

        // Se configuran los alias, para cargar el código fuente y no las versiones de distribución.
        if (!webpackConfig.resolve.alias) webpackConfig.resolve.alias = {};
        webpackConfig.resolve.alias['parchment'] = path.resolve(this.root, 'node_modules/parchment/src/parchment.ts');
        webpackConfig.resolve.alias['quill$'] = path.resolve(this.root, 'node_modules/quill/quill.js');

        // Se agregan las extensiones para resolver archivos Typescript y SVG atuomáticamente.
        let end = webpackConfig.resolve.extensions.length;
        webpackConfig.resolve.extensions.splice(end,0,'.ts', '.svg');
    }
}

// Se configura con método 'mix.editor()' por ahora solo consiste en configurar para Quill.
mix.extend('editor', new QuillMix());
