# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).





## [1.0.2] - 2020-07-27
### Changed
- Refactored the code to respect the single responsability principle. API remains the same.

### Fixed
- The urls of the pictures contain query variables that may change from time to time. 
  Instagram::unique() was changed to take that in consideration.





## [1.0.1] - 2020-02-08
### Fixed
- In some servers, instagram would redirect the request to the login page. Adding a sessionid 
  cookie to the request headers seems to have fixed it.