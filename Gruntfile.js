'use strict';

module.exports = function (grunt) {
    const sass = require('node-sass');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-regex-replace');

    grunt.initConfig({
        'sass': {
            'options': {
                'implementation': sass
            },
            'backend': {
                'files': {
                    'Resources/Public/Css/Backend/backend.css': 'Resources/Private/Scss/backend.scss'
                }
            }
        },
        'cssmin': {
            'options': {
                'sourceMap': true,
                'mergeIntoShorthands': false
            },
            'backend': {
                'files': {
                    'Resources/Public/Css/Backend/backend.min.css': 'Resources/Public/Css/Backend/backend.css'
                }
            }
        },
        'regex-replace': {
            'backend': {
                'src': 'Resources/Public/Css/Backend/backend.min.css.map',
                'actions': [
                    {
                        'search': new RegExp(/Resources\/Public\/Css\/Backend\/backend\.css/),
                        'replace': 'backend.css'
                    }
                ]
            }
        },
        'watch': {
            'scss': {
                'files': 'Resources/Private/Scss/**/*.scss',
                'tasks': ['sass', 'cssmin']
            }
        }
    });

    grunt.registerTask('default', ['sass', 'cssmin', 'regex-replace']);
};
