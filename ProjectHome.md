The general idea for **Garden** ("yet another new php framework this week") is to create a low-level framework upon which custom web applications could be built, possibly using another, high-level framework. It is expected to provide:

  * Proper error handling and logging (to screen, email and/or database)
  * Easy data access via objects
  * Object persistence
  * Integrated, database-level data validation
  * Automatic creation and modifications of database tables based on class definitions
  * Integrated developer tools like variable pretty-printing, more usable error messages with stack trace and code snippets, API docs generator, database browser
  * Full system dump on errors, including request data, defined variables, functions, memory usage at the moment of crash, runtime, etc.
  * Helper classes to facilitate javascript-like manipulation of arrays, string formatting and more usable callbacks
  * Other dreamy tools