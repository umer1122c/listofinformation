<!DOCTYPE html>
<html lang="en">

<head>
    <title>Contact Email</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>

<body style="font-family: 'Roboto', sans-serif; background: #f1f1f1;">
    <main>
        <!----- CONTACT-SECTOION-START ------>
        <section class="contact-bg">
            <div class="container">
                <div style="background: #fbfbfb;width: 60%;margin: 40px auto;box-shadow: 0 2px 8px hsl(0 0% 0% / 16%);padding: 26px;height: 100%;">
                    <div style="text-align: center;">
                        <a href="javascript:void(0)"><img class="flogo" src="{{$url}}/front/assets/images/logo.png" alt="logo.png"></a>
                    </div>
<!--                    <div style="text-align: center;">
                        <img style="width:15%;margin: 22px 0 0 0;" src="{{$url}}/front/assets/images/answer.png" alt='email'>
                    </div>-->
                    <div class="contact-content">
                        <h2 style="font-weight: 700;font-size: 36px; margin-bottom: 0; text-align: center;">Send us a Message </h2>
                        <ul style="list-style: none;">
                            <li style="display: inline-block;">
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">
                                    <Strong>Name</Strong>
                                </p>
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">{{$parameaters['name']}}</p>
                                <p>
                            </li>
                            <li style="display: inline-block;">
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">
                                    <Strong>Email</Strong>
                                </p>
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">{{$parameaters['email']}}</p>

                            </li>
                            <li style="display: inline-block;">
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">
                                    <Strong>Phone</Strong>
                                </p>
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">{{$parameaters['phone']}}
                                </p>


                            </li>
                            <li style="display: inline-block;">
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">
                                    <Strong>Message</Strong>
                                </p>
                                <p style="padding: 0 78px;line-height: 23px;font-size: 14px;color: #131313;">{{$parameaters['message']}}</p>
                            </li>
                        </ul>
                        <div style="text-align: center; padding: 50px 0; font-weight: bold; font-style: normal; font-size: 14px; margin: 6px 0 0 0;">
                            <img src="{{$url}}/front/assets/images/flogo.png" style="width: 40px;">
                            <address style="font-style: normal;">12A Mabinuori Dawodu Crescent Gbagada</address>
                            <address style="font-style: normal;">Contact us: 
                                <span style="font-weight: 500;">+2347045000137</span>
                            </address>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!----- CONTACT-SECTOION-END ------>

    </main>
</body>

</html>