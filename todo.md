# TODO

* Currently there is no limit on how many lines of output are produced. This might make a denial of service attack possible.

* The application is hardcoded to work with UTF-8

* Key listings (index, vindex) list keys unordered, just what gpg outputs.
  Order them? By what field?

* /admin/remove requires a POST request with fingerprint as param. Actually this should be a DELETE request. Do all browsers handle `<form method="delete">` correctly?

* The lock image in the submit key textarea is taken from https://www.iconfinder.com/icons/314814/lock_icon#size=256
  License: Free for commercial use (Do not redistribute)
  Make sure this license is sufficient.
