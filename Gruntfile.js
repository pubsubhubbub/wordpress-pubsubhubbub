module.exports = function(grunt) {
	grunt.initConfig({
		wp_readme_to_markdown: {
			target: {
				files: {
					'readme.md': 'readme.txt'
				},
			},
			options: {
				screenshot_url: 'https://ps.w.org/pubsubhubbub/trunk/{screenshot}.png'
			},
		},
		replace: {
			dist: {
				options: {
					patterns: [
						{
							match: /^/,
							replacement: '[![Build Status](https://travis-ci.org/pubsubhubbub/wordpress-pubsubhubbub.svg?branch=master)](https://travis-ci.org/pubsubhubbub/wordpress-pubsubhubbub) [![Issue Count](https://codeclimate.com/github/pubsubhubbub/wordpress-pubsubhubbub/badges/issue_count.svg)](https://codeclimate.com/github/pubsubhubbub/wordpress-pubsubhubbub) \n\n'
						}
					]
				},
				files: [
					{
						src: ['readme.md'],
						dest: './'
					}
				]
		  }
		},
		makepot: {
			target: {
				options: {
					mainFile: 'pubsubhubbub.php',
					potFilename: 'pubsubhubbub.pot',
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-replace');

	// Default task(s).
	grunt.registerTask('default', ['wp_readme_to_markdown', 'replace']);
};
