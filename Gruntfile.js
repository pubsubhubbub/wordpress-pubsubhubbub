module.exports = function(grunt) {
	grunt.initConfig({
		wp_readme_to_markdown: {
			target: {
				files: {
					'readme.md': 'readme.txt'
				},
			},
			options: {
				screenshot_url: 'https://s.w.org/plugins/{plugin}/{screenshot}.png'
			},
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

	// Default task(s).
	grunt.registerTask('default', ['wp_readme_to_markdown']);
};
