module.exports = function ( grunt ) {

	// Import the package settings and the staging configuration
	var package = require ("./package.json");

	// Load NPM tasks so we can use them in our configuration
	grunt.loadNpmTasks ("grunt-contrib-clean");
	grunt.loadNpmTasks ("grunt-contrib-compress");
	grunt.loadNpmTasks ("grunt-contrib-watch");
	grunt.loadNpmTasks ("grunt-git");
	grunt.loadNpmTasks ("grunt-replace");
	grunt.loadNpmTasks ("grunt-mkdir");
	grunt.loadNpmTasks ("grunt-rsync");

	grunt.task.registerTask ( "default", "preform a full build", [ "release" ]);

	grunt.task.registerTask ( "deploy", "upload src contents to staging environment", function () {
		// Make sure that staging object is set in package JSON file
		if ( package.hasOwnProperty ( "staging" ) && package.staging.user.trim () != ""
		&& package.staging.host.trim () != "" && package.staging.port.trim () != ""
		&& package.staging.dest.trim () != "" ) {
			// Initialize the rsync option object
			var _rsync_options = {
				args:       [ "-az", "-o", "-g", "-e 'ssh -p " + package.staging.port + "'" ],
				exclude:    [],
				recursive:  true
			}
			// Save it into grunt config
			grunt.config.set ( "rsync.options", _rsync_options );
			// Traverse through jetrails dependencies
			package.jetrailsDependencies.forEach ( function ( dependency ) {
				// Extract the dependency name
				var name = dependency.match (/\/([a-zA-Z0-9-]*)\.git$/) [ 1 ].toLowerCase ();
				// Initialize the rsync dependencies options
				var _rsync_dependencies = {
					src:        "lib/" + name + "/src/*",
					dest:       package.staging.dest,
					host:       package.staging.user + "@" + package.staging.host
				}
				// Configure the dependency rsync options
				grunt.config.set ( "rsync." + name + ".options", _rsync_dependencies );
				// Run the dependencies task
				grunt.task.run ( "rsync:" + name + "" );
			});
			// Initialize the rsync staging options
			var _rsync_staging = {
				src:        "src/*",
				dest:       package.staging.dest,
				host:       package.staging.user + "@" + package.staging.host
			}
			// Save it into grunt config
			grunt.config.set ( "rsync.staging.options", _rsync_staging );
			// Run the task and upload the main source files
			grunt.task.run ( "rsync:staging" );
		}
		// Otherwise, report that we cannot deploy
		else {
			grunt.log.writeln ( "could not find staging details in package.json" );
		}
	});

	grunt.task.registerTask ( "stream", "watch src and lib folders, deploy on change", function () {
		// Initialize the watch task options
		grunt.config.set ( "watch.module.files", [ "src/**/*", "lib/*/src/**/*" ] );
		grunt.config.set ( "watch.module.tasks", [ "deploy" ] );
		grunt.config.set ( "watch.module.options.spawn", false );
		// Run the task
		grunt.task.run ( "watch:module" );
	});

	grunt.task.registerTask ( "version", "update version, defined in package.json", function () {
		// Initialize the replacement patterns
		var _patterns = [
			{
				match:              /^(\s*\*\s+@version\s+)([0-9]+\.[0-9]+\.[0-9]+)(\s*)$/gm,
				replacement:        "$1" + package.version + "$3"
			},
			{
				match:              /^(\s*<version>)([0-9]+\.[0-9]+\.[0-9]+)(<\/version>\s*)$/gm,
				replacement:        "$1" + package.version + "$3"
			}
		];
		// Initialize which files are effected
		var _files = [
			{
				expand:             true,
				flatten:            false,
				src:                [ "src/**/*.php", "src/**/*.phtml", "src/**/*.xml" ],
				dest:               "."
			}
		];
		// Update the options in grunt config
		grunt.config.set ( "replace.version.options.patterns", _patterns );
		grunt.config.set ( "replace.version.files", _files );
		// Run the replace task
		grunt.task.run ( "replace:version" );
	});

	grunt.task.registerTask ( "init", "initialize file structure", function () {
		// If no arguments are passed set the default config
		if ( arguments.length == 0 ) {
			grunt.config.set ( "mkdir.execute.options.create", [ "src", "dist", "lib" ] );
			grunt.task.run ( "mkdir:execute" );
		}
		// Check to see if there is one argument
		else if ( arguments.length == 1 ) {
			grunt.config.set ( "mkdir.execute.options.create", [ arguments [ 0 ] ] );
			grunt.task.run ( "mkdir:execute" );
		}
		// Warn user if there is more than one argument
		else {
			grunt.log.writeln ( this.name + ": No argument or one argument is valid" );
		}
	});

	grunt.task.registerTask ( "nuke", "clear generated file structure", function () {
		// If no arguments are passed set the default config
		if ( arguments.length == 0 ) {
			grunt.config.set ( "clean.execute", [ "dist", "lib" ] );
			grunt.task.run ( "clean:execute" );
		}
		// Check to see if there is one argument
		else if ( arguments.length == 1 ) {
			var paths = [ arguments [ 0 ] + "/**", arguments [ 0 ] + "/.*" ];
			grunt.config.set ( "clean.execute", paths );
			grunt.task.run ( "clean:execute" );
		}
		// Warn user if there is more than one argument
		else {
			grunt.log.writeln ( this.name + ": No argument or one argument is valid" );
		}
	});

	grunt.task.registerTask ( "resolve", "downloads jetrails dependency extensions", function () {
		// Run the dependency command
		grunt.task.run ( "init" );
		// Loop through all the dependencies
		package.jetrailsDependencies.forEach ( function ( dependency ) {
			// Extract the dependency name
			var name = dependency.match (/\/([a-zA-Z0-9-]*)\.git$/) [ 1 ].toLowerCase ();
			// Check to see if we already cloned the repository
			if ( grunt.file.exists ( "lib", name ) ) {
				// Nuke it
				grunt.task.run ( "nuke:lib" );
			}
			// Set the configuration, and clone it into lib
			grunt.task.run ( "init:lib/" + name );
			grunt.config.set ( "gitclone." + name + ".options.repository", dependency );
			grunt.config.set ( "gitclone." + name + ".options.directory", "lib/" + name );
			grunt.task.run ( "gitclone:" + name );
		});
	});

	grunt.task.registerTask ( "release", "prepares file structure for github release", function () {
		// Run the dependency tasks
		grunt.task.run ( "init" );
		grunt.task.run ( "resolve" );
		// Change version numbers
		grunt.task.run ( "version" );
		// Clear dist folder
		grunt.task.run ( "nuke:dist" );
		// Initialize the files array for compression
		var files = [{
			cwd:            "src",
			expand:         true,
			src:            ["**"]
		}];
		// Traverse through jetrails dependencies
		package.jetrailsDependencies.forEach ( function ( dependency ) {
			// Extract the dependency name
			var name = dependency.match (/\/([a-zA-Z0-9-]*)\.git$/) [ 1 ].toLowerCase ();
			// Append to the files array
			files.push ({
				cwd:            "lib/" + name + "/src",
				expand:         true,
				src:            ["**"]
			});
		});
		// Define the output file
		var company = package.company.replace ( "®", "" );
		var name = package.name.replace ( "-", "_" );
		var _output = company + "_" + name + ".tgz";
		// Define other options
		grunt.config.set ( "compress.module.options.archive", "dist/" + _output );
		grunt.config.set ( "compress.module.options.mode", "tgz" );
		grunt.config.set ( "compress.module.files", files );
		// Compress the module
		grunt.task.run ( "compress:module" );
		// Nuke the lib folder
		grunt.task.run ( "nuke:lib" );
	});

};