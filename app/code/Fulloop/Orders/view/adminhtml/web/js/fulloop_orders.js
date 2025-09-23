function createVstOrder(url, incrementId, tokenType, accessToken, adminUserId)
{
    console.log(incrementId+'||'+tokenType+'||'+accessToken+'||'+adminUserId);

    // var settings = {
    //     "url": url,
    //     "method": "POST",
    //     "timeout": 0,
    //     "headers": {
    //         "Content-Type": "application/json"
    //     },
    //     "data": JSON.stringify({"increment_id":incrementId, "token_type":tokenType, "access_token":accessToken, "is_ajax":1}),
    // };
    //
    // $ajax(settings).done(function (response) {
    //     console.log(response);
    // });

    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");
    var raw = JSON.stringify({"increment_id":incrementId, "token_type":tokenType, "access_token":accessToken, "admin_userid":adminUserId, "is_ajax":1});

    var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: raw,
        redirect: 'follow'
    };

    fetch(url, requestOptions)
        .then(
            response => {
                response.text()
                alert('success:' + incrementId)
                location.reload()
            }
        )
        .then(
            result => {
                console.log(result)
                location.reload()
            }
        )
        .catch(error => {
                console.log('error', error)
                alert('error')
                location.reload()
            }
        );
}
