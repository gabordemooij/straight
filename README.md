# The Straight Framework v1.2 'Franklin'

The Straight Framework

An advanced, NO-NONSENSE, functional PHP micro framework for minimalists.

The framework consists of:

- a folder structure
- an htaccess file for routing (for Apache servers)
- a minimalist router function
- a function for retrieving and processing both settings as well as text
- a view function
- an utf8 user data escape function
- a basic query function
- a shortcut function for JSON output (jout)
- a function to output a file
- a test function

When deploying straight in a subfolder, one can edit the fmap call in routes.php to:

```
fmap( [ '/subfolder/' => '' ]...
```

