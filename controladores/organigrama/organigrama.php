<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/organigrama/organigrama.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/nomina/incidencias.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/nomina/trabajadores.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/con_edosfinancieros.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/modelos/Clases/sucursales.php';

define("PERFIL_GEN",'
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAYAAAD0eNT6AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAN1wAADdcBQiibeAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAETOSURBVHja7Z13eFVHmqfXnrbbbXt7Zntnume6t3d2t6dnDc7Z60ZZZCSSQAgQOQcDNtgGYzImCCSRRDYYEwwYgw022CCEAAHCZBAZBUBkUBaZ2jrXFi2wwtU9de494f3jfdzPdEP3nFtVv7eqvqr6L0KI/wIA1qJtSMhzbYKDe0cHBc2XrG4dGJgs2d0qMPBoq4CArJYBAZda+vvnRvn7F7fw87vVokaNu5E1aojmf/tbmWj/XpSfn5B/Vsi/717b4OAb7UNDczrWqnW6c506B7vVq7epe/36qySfSxIk4ySDJX0kzSUvSZ7ktwGwDnwEAPOG/D/LIG7WJigoTob7BhnO6TLQC2VY3ysvyCtD/nkt4EW70FDRqXZt0aVuXSHDXcjwVsE9yWnJeslUSW9JLcm/Sx7hNwVAAADgl2H/tDajl0G/Sc7cr8hZ+21PQ/7hsJczedFZhr3CoPeEYskBySJJZ8l/8LsDIAAATgz8x2Xgt5cBvU4L/IqW591FW753hX2dOr4Oe3fRVgvmS9pI/gftAgABALBj4D/aNjg4Qgb+ypYBARf0LOOX3rfXZvgdata0SuBXxnHJTEmk5Pe0GwAEAMCyaLP81oGB+7UiPL2BX7KsL0XCtX9vg8CvjN2SvsgAAAIAYJXZ/lvRgYE/RPn53VQV+lrRnlaw54DQL4vbkjU/nzZ4gjYGgAAAmCn0/y06KGhuS3//HBWhr9E6MNApM/2qkCuZJalBuwNAAAB8Wcg3uFVAQKaKIj6NFn5+ol1IiOhqjz19ozklGSb5C+0RAAEA8MqRPTnbX6BdsKNytt+xVi1C3fP7B1ZKXqN9AiAAAEYE/+9k8K+QwX9HRehrqwZaQV9X5+7tG8E6tgcAEAAAlfv736mq5I+UtNGCn2V+I9GuLq5J+wVAAAA8Cf7/rd3Mp+LMfgnauX1m/F4lVRLOVcQACACAO8H/nzL4d6gO/i4Evy/ZL2lG+wZAAADKvK1PK+5TGfxacV+XOnUIYPOwWfIs7R0AAQD4KfyDgyOj/P0LVAV/S39/1wM8BK5pLxaKkTxF2wcEAMDBBX5ylr5XVfBrlf3agzyErCU4I2lKPwAEAMBhtAkKim9Ro8YdVeGvvcZHgZ8lWctlQoAAADhjub+m9gSvylm/9iIfQWpprkuGSn5NHwEEAMB+y/2Ptw4M3BipKPhLivyY9duKExJ/+gsgAAD2Cf+X5aw/V1Xwt5Czfq7utS13JIMlj9J3AAEAsPJef3DwoEhFt/iVzPq7cYufE1gv+QN9CBAAAGsu+W9rrnDJX3upj2B0FOclQfQnQAAArBP+r7f0989TWejXiXP9TuXuzwWCbAkAAgBg8iX/oSqX/LVLfSj0A0mi5F/pY4AAAJhv1v+Edoe/yiV/7Q5/9vuhFBckofQ3QAAATHSjn5ypX1UZ/tzoBxVsCfSi3wECAOD78K8e5e9fpPKIH/v94AYj6H+AAAD47lY//xZ+freUhb+fH/v9UBVmUhwICACA98M/ooXCYr8oiv3AM1ZwhTAgAADeq/TvGVmjxj2llf4U+4HnJEl+S98EBADA2PD/ROV9/lr4U+kPCtjLzYGAAAAYRHRQ0HyVlf7aE76EPyjkJE8LAwIAoD78v1Ya/tzpD8bdFVCNPgsIAICa8J/bXPEzvgQVGMgZyZ/pu4AAAOjb8x9J+IMFOSz57/RhQAAAPAv/HtpDPMoK/tjzB++yQ/IUfRkQAICqnfNvovKoX5R2yQ/hD95nreQx+jQgAADuhX+NFjVq3FH5nG8XLvkB37FQ8gh9GxAAgIrv9n+mhZ/fTWXhL+FufzABsfRvQAAAyg//P0T5+xeqLPrrULMm4QNm4UP6OSAAAL8M/8db+vtfURn+7UJCCB0wG23o74AAAJSidWDgVpXhHx0URNiAGSmWPE+fBwQA4KfjfgNVX/FL0ICJOSp5mr4PCAA4fen/5UiFz/pqFf886wtWOBlA/wcEAJy+75+rcvbfsVYtwgWsQifGAUAAwKn7/ikqw78N+/5gvXqAFxgLAAEAp+37D1IZ/lH+/lzzC9QDACAA4Kh9f0nnOnUIE7AqixgXAAEA9v09oH1oKCECVqcz4wMgAGD3ff+NSo/88bwv2IPrkv9kjAAEAOw5+w8ODopUGP4ttCN/7PuDfVjPOAEIANiSlgEBl7jnH6BCmjNWAAIA9qr6Dwoay21/AJVyllMBgACAnQr//rmFn99tlQJA1T/YmAmMG4AAgF0K/7YrvfAnOJiQADtzW/IcYwcgAGD1wr96KsNfu+ufC3/AAWxm/AAEAKxd+Ofvf40z/wAe0YYxBBAAsCTRQUFTVIa/lAlCAZzERck/MZYAAgBWK/z7txYKr/vV6FS7NqEATmMK4wkgAGC12f86leEfzUt/4ExuSf4nYwogAGCZY3+Rimf/XevWJQzAqUxlXAEEAKwy+/+K2T+A0ncC/pWxBRAAMPvs/+kWfn53VApAFy79AYhhfAEEAMw++1+oMvxb89ofgEaB5L8zxgACAGad/T+h/MpfKv8BShjBOAMIAJh19j9T6bl/HvwBKE2O5LeMNYAAgNlm/7+K8vO7qVIAOtaqxaAP8CADGW8AAQBT0SYoKFZl+Edx6x9AWVySPMmYAwgAmAYZ2MUqBaBDzZoM9gBl05cxBxAAMMfsPzi4vcrwb+HnxyAPUD6HGXcAAQBT0Dow8IBKAWgXEsIgD1AxrzH2AAIAvi7+ezKyRo17Si/+4dpfgMqYzPgDCAD4evl/DEf/ALzOZcljjEGAAIDPaBUQkK1SANqHhjK4A7hHOGMQIADgq+X/v6gMf9erf/XqMbADuMeXjEOAAICvbv77QmX4t+Lef4CqcEPy3xiLAAEAX5z9L+DsP4BP6cZYBAgAeHf5Pzi4lsrwj6xRQ3Rj+R+gqmxjPAIEALx99n+rSgGIDgpiMAfwjL8yJgECAF6jBQ//APBAECAA4Ljq/9dVL/8ziAN4zAbGJUAAwFvV/zOVVv9z+Q+AHoolv2ZsAgQAvHH5zwnu/gcwFYGMTYAAgDf2/2+rFIDOtWszgAPoYzhjEyAAYPTxvyD2/wFMxxbGJ0AAwOj9/wXs/wOYjluSpxijAAEAI/f/s9j/BzAltRmjAAEA4/b/a9S4y/4/gCkZxxgFCAAYtf8fxv4/gGnZyTgFCAAYtf+/jP1/ANNyR/KPjFWAAIAR+/8Z7P8DmJqajFWAAIByovz9i7j/H8DU9GKsAgQAlBNZo8Y9lQLQpW5dBmwAtUxhrAIEAFQ/APSyyvDXYLAGUM4PjFeAAIBS2gQH91UZ/lF+fgzWAOrJZLwCBABUnwBYqFIAWgcGMlgDqOee5DeMWYAAgDJkYO9WKQBtg4MZrAGM4QXGLEAAQBktAwIuqRSA9jVrMlCDi4FRTcXa2HdF2pJhIjnhfTH3wy58F300Y8wCBADUXQHs53dLpQB04gpgx9OzQX2xbdZAcXX9eHH1h3EPcHLFKNE7PIzv5BmDGbMAAQBVJwCeVn0CoCtHAB3NsLaRInvNmF8Ef2n2fT6Eb+UZnzNuAQIAvAEApuPLUb1kwI+vMPxLmPNBZ74ZbwIAAgA+PAI4UqUAtPT3Z5B2IP2aNBRHlg53K/hLuLRunOjZoAHfr2pcZdwCBABUHQGcofQRII4AOo7J77QTl78fV6XwL2HD5P58wyo+CsS4BQgAqBKAL7gDADwlefoHHgX/fdbHiHebNuRbVo0nGbsAAQAVAvCdSgGQfx8DtAMY3i5SnFn9ia7wv7Z+vCjaMlmsjR/AN60af2DsAgQAVFwCtFWlALThEiDbs3HqgDKP91WF/KRYcTN1urj940xxY8d0Mbh1M76t+/wHYxcgAKBCAPYpvQUwJIQB2qbE9owW578bqyv4czbEiOvbprqCvzS7FwzjG7vPy4xdgACAbloFBBxXKQDtEADb0adRuNg9f7C+vX5J4eZJ4vbOGb8I/xJie7ble7uHP2MXIACgQgDOKL0GODSUAdpGfDqwi8cV/iXkbpwobu5IKDf4S8hcPV704FigO9Rn7AIEAPS/A+Dvf1mlAHTgHQBb8H5kE3F02QjdRX7FKVMqDf7SfDW2L9+/clowdgECACoEIF+lAHSsVYsB2uJ3+Gtn8929za/cIr/kOHGrguX+8riZOkPEdI/mt6iYzoxdgACAbqL8/K4jAKCxYHA31+18uor8EieIG9unVTn4S3M5MV70b9aY36R83mXsAgQAVLwEeJuXAJ3N+G6txelv1Jzp1xP8pdm/eITo0YDfphyGMnYBAgD6BaBGjbsqBaAzAmAZPmjRRBxaPFR3dX/+prj7Z/pV8vX4fvxOZTOGsQsQAEAAoMr0CgsTyQkf6N/n14Lfjep+Pcz/qBu/2S8Zy9gFCAAgAOB+gV9YA7Eu7l3dx/p+Cv7phgZ/6aLAyX3a8/shAIAAAAIAPg3+VO8Ef2kKU6aJTzq34rdEAAABAAQA3A7+WAXBn+yb4C/NtaTJvBeAAAACAAgAVBb8axUEf0FyvM+D/+HjgcPaRvIbIwCAAAACAKXpH9FIbEp433bBX5rc5ClsByAAgAAAAgAacb3aiCNLh+t7olf+WS34b5k0+EtTsHWamNizDQIAgAAAAuA8eoeHiW/G9RHnvtX/PG/hlkky+GeYPvhLU7x9upjWrwMCAIAAAALgDLQ98J+e5tV3hj8vKVZc3zbNUqFf1hFBh94TgAAAAgAIgHPu6e8qMleNVjTbn27p4H+YpISBold4GAIAgAAAAmCTor7mjZUU9f00259qq9B/mJMrx4pBLSMQAAAEABAA6xLfu63uoj67zvYrOyEQ37sdAgAIAB8BEADrMCgqQmyY3F93UZ8TZvsVcWvnDLFybF+7vySIAAACAAiAlfk4upnYOHWAyF4zRlfo526cKIq2TnbUbL8yDiwZKT5qFYEAAAIAgACYp4o/efoHCmb6WuhPcc14CfyyKUpJEEtH9nbdjIgAAAIAgAB4nZEdWoitMz8U578bq+uiHm15vziF0K8qp74eJ0Z1bIkAAAIAgAAYz5jOLcX22QPFhbWez/KvydDP36SF/lRCX8GdAWvjB4h3GoUjAIAAACAAapnSp51InTtIXFynI/Q3xLie3XUV8hH6yrnwQ5yY9E47BAAQAAAEQN8sP3FKf5HhuqBnvMezfG1pXyviu7kjgZD2EoeWjhJju7RGAAABAAQAAaicwa2aiTUxfcXx5SPEFU+v4tUCf+NE1xn9G9sJfF+z5/PhYmSHKAQAEABAABCAB8/mLx/VSxxaMkxcWud58Z52TK9w8yRxffs0lvVNeXfATLHj0yFiSJvmCAAgAIAAOE0A+kc0ct23rxXuaXfuezrDz02cIPKT41xH9G6wpG+5QsHNMz+ywpXCCAAgAIAAeEKfRuFi5oCOIjnhfXFyxSiP79rPSfypYE/bv78hZ/dU6tuD69unSxEYJEa0b4EAAAIACIDVBEC7/GVc15Zi6YieYsuMD8ThL4aLs2vGVHlmf23DT0v4WtBr+/bakTxtZk/YO4ODX4wSU/q2N9vVwggAIADgbAHoKQflTzq1FIuH9XDN5tOWDBNnVo+p8oz+2npCHirm7NqJYvYHXRAAQAAAATBaALSKe22ZftnInmJt7LsiZeaHYt/nQ8SxZSPEmW8+cS/kZbDnJE5wVd1rF+oUJMe5ivGKtkx23ainVeET8lAVTqwcK8Z3a40AAAIACIAqARgY1VRskrP47G/HeHymvuQ1PC3cb/IwDhhI6rwhok/jcAQAEABAADwVAG0Q1fbn9TyMo6Et3zObB29fJtS7YRgCAAgAIABVFYAJPaI9P19fav9em/ETSOCTi4QWDhe9whpckO35CgIACAAgAG4MYtq+vrZHryf8tTP3LPWDr9m1YFiul1cAxjB2AQIAlhSAvQs+1r3kr+31Ez5gFrxcGMgKACAAYD0BmDWgo+7w15b9bzHzBxOxf/FIVgAAAQAEoLyBq3d4mO49fw3t5j1CB8zGJ51bIQCAAAACUBY7P/1Id/hrF/YQNmBGtn/6MQIACAAgAA8T0621rvP9JdzkgR0wKdlrJ1IDAAgA2F8AIiW9wmqJcV2aic8GdRCffthOTOvbWiT0bSuGRjf7xaCl3eKnu/CP2T+Y/DXBXuEP3gswJLq5mPthV7FwaE+xfPQ74qsxfcWM/p1c/3cd7wsgAIAAgPcFYECz+mL1uJ4ie83oCsP61IoRYt3EvmJEu0jXoKVk738Le/9gboa1ayGGtY0U66d8IM6tq/ikSmHKNJEye7AnJwgQAEAAwDsCEB0UICa/01LsWzCoyqF95ftxInlaf93hr3GD5X8w/WmAEa6VgKr+uaw1MWLxsJ6iX5OG1AAAAgC+F4DoQH+xaEinSmf7lZGzIUaJAHDVL9idopQE8f2k90XfikWAFQBAAMA4ARjTKUKc/HKYkuDO3ThByd9DQIBTuLJxUkXPDyMAgACAegHQCvu2TH9PSWCrFIBrG8YTDOA40paNdtUVIACAAIChAjCtbytxce0YpeFfcnYfAQDwjBs7poulI3sjAIAAgHoBaOFXQ3w1urvy4EcAANSRPGOQ6BnWAAEABADU0CE06G7KjP6GhT8CAKB2S2BQy4h4xi5AAEAXMlj/48iSIYaGPwIAoJacTZOvyn/+B2MYIADgafj/XpJhdPgjAACGkCn5V8YyQACgquH/hGS7N8IfAQAwjF2SpxjTAAEAd8P/EclSb4U/AgBgKN9IHmVsAwQA3BGA0d4MfwQAwHCmMLYBAgCVhX8tb4c/AgDgFZoxxgECAOWF/28kpxAAAFtyTvJbxjpAAKAsARjri/BHAAC8xlTGOkAA4OHwf0FyGwEAsDV3JW8w5gEfAUpX/e/wVfgjAABeZS+nAoCPACUCEOrL8EcAALxOA8Y+BABAE4CvEAAAR/EdYx8CAIT//5DcQQAAHMU9yV8YAxEAcLYAjPR1+CMAAD4hhjEQAQDnhv9jkgsIAIAj0V4MfIKxEAEAZwpAkBnCHwEA8Bl1GQsRAHCmAAxHAAAczTjGQgQAnCkAyQgAgKNJZSxEAMB54f+E5AYCAOBobkueZkxEAMBZAhBolvBHAAB8Sh3GRAQAnCUAgxAAAJCMYExEAMBZAjAFAQAAyWzGRAQAnCUAixEAAJB8xZiIAICzBOAHBAAAJJsZExEAcJYA7EYAAECSxpiIAICzBCATAQAAyQXGRAQAnCUAlxAAAJDkMSYiAOAsATiKAACA5CRjIgIAzhKAFAQAACTbGBMRAHCWAHyNAACAZBVjIgIAzhKATxEAAJDMYkxEAMBZAhCDAACAZCRjIgIAzhKAzggAAEh6MiYiAOAsAfhPBAAAJC8zJiIA4DwJOIcAADj7EiDJI4yHCAA4TwCWIAD25nrKVFG4eZLI3xQrCrdMEje2T+O7QGnmMxYiAOBMAeiKANiTm6nTRV4537RAysCtnXwjcNGCsRABAGcKwF8RAHuSkzihwm+lrQjwnRzPXcnvGAsRAHCuBGxGAOxF0ZbJbn2v69um8r2czXbGQAQAnC0AkQiAvchz81vmJ8fxvZxNB8ZABACcLQCPmeE0AAKgjmvrx7v3zRMn8r2cS5bkMcZABACQgOEIgH1w93vlJMbwvZxLD8Y+BABAE4A/Sm4hAAgAOIJzkicY+xAAgBIJGI0AIADgCN5lzAM+ApQWgCckJxEABABszUXJU4x5wEeAhyWgJgKAAIBtuSepz1gHCACUJwGLEAAEAGzJRMY4QACgIgH4veQ8AoAAgK3YybE/QADAHQl4XVKEACAAYAvyJP+HsQ0QAHBXAhpL7iIACABYnuaMaYAAQFUl4F0EAAEAy1IkiWYsAwQAPJWAqQgAAgCW44TkecYwQABArwQMltxDABAAsASrJP/I2AUIAKiSgAhJMQKAAICpGSF5hDELEABQLQGvGfVyYO7GCQgAAgCec1fSk3EKEAAwUgL+JPlOuQAkKhCA9QgAAuBIblLpDwgAePuYYKaZBECDMEAAHEaOJJQxCRAA8LYEPPnzK4I39Qa3FkYqBODWzhkIAALgBIol4yS/YywCBAB8KQJ/lSRI8jwXgAl3lAhAKgKAAJieWzr/7HTJHxl7AAEAs60IdJDsqEJoa8H/WX5SbLgKAbiZOh0BQADMjr9kruROFf7MQUk/yb8w1gACAGaXgRckwyXLJPtLHSHU/rlPsvTnf/8Z7T9fuHnSn1QIwI0dCQgAAmB2fq+1efnPv0qGSpZI9kgKf/73b/wc+MslIyWvMaYAAgBWFoJHfn5psMwzylIAfqVEALZPQwDcFYD140VxytQKub5tGnUV6vlNWX1AO7+vzfAljzJmAAIAjkI7xqdXAK5vm4oAGHFPQ9JEcXMH2ysKuE1fBwQA4GEB2BCjuxCwOGUKAmDQbY3XWGFRwWX6OiAAAA+Rmzjhmt6QKtw8CQEw8M0GKWlsCehjG30dEACAh8hLik3TG1D5m2IRAINfbmSVRRfz6OuAAAA8RP6muJUqbhREAIwVgILkeILccz6krwMCAPAQMlgG8x6AfrRleiMFIH8TAqCDRvR1QAAAfnEUMN6fy4D0k5c00eAtgMkEuedUo68DAgBg0F0A2tl1J4eMtkdvWBHgeilYFAF6inbz3+P0dUAAAMogJzHmut6QKtrKDDUvKdaY2f9WCgB1cII+DggAQHlHATdOPEORmn5u/ThDFGyOF1fXq5r5jxfF2wh/naymjwMCAFD+SYCvOQmgjpupM2RwTxWFWya57kioKkVbJrtuV7y1k2+pgEH0cUAAAMo7CbA5PlLJs8DsU4P5eIs+DggAQPmFgI9eWz/+Hm8CgM3Il/yKPg4IAEDFdQBnqQMAm7GGvg0IAEDldQBf6BWAHOoAwFy8S98GBACg8jqAekrqAFKpAwDT8BJ9GxAAADe4tmH8XR6tAZtwRfII/RoQAAA3yNs4MUP3nfXJcYQPmIEv6dOAAAC4uw2QHBej/+16HgYCU9CKPg0IAID7xwGf5jgg2OT435P0aUAAAKqyDZAUm6b/6dpYQgh8yTz6MiAAAFXeBojvrPs0wPrx3AoIviSIvgwIAIAH5GyIucULdmBRsqj+BwQAwONLgWITdT8OtHEiYQS+YDR9GBAAAI+LAeNrqLgU6GbqdAIJvM3/pQ8DAgCg522AxAk5egVAew6XQAIvsoO+CwgAgP5iwGG67wRYP17cphgQvEdT+i4gAAAqigETJxTrlYCirZMJJvAGhyWP0m8BAQBQUQyYHDdB9wuBG2IIJ/AG0fRZQAAA1J0G+AcZ4Df0rwJwJBAMJV3yK/osIAAASiUgbjqrAGByutJXAQEAUF8M+GslFwPxTDAYQ7bk1/RVQAAAjFkFWKB7FSBxAmEFRtCPPgoIAIBhqwBxT+ZsiLnJKgCYjAzJb+ijgAAAGCsB/VTcC8AjQaCQBvRNQAAAvHE74MaJmXoloCA5nuACFayiTwICAOCtVYDN8S/KWfw9vRJwY3sCAQZ6KJL8T/okIAAA3i0IXKH7pUAKAkEfH9IXAQEA8DKFmyc9npMYwxXB4Msrfx+jLwICAOCbuwE6KSkITKUgEKpMIH0QEAAAH5KXFLtTrwTIv4NAg6owhb4HCACA77cCnsxNnJCveytgC1sB4Ba7uPEPEAAA80jAW2pOBUwj4KAiciV/oc8BAgBgrnqAkSoeC+KCIKiACPoaIAAA5qwH+JF6ADCIqfQxQAAAqAcA9v0BEAAAE9YD3KUeABRxlX1/QAAArHNVcEu9RYHa/QA3d3BVMFf9znyLPgUIAIC1igLf031J0IYYcTN1OkHoTG5J6tKXAAEAsKQExMXqPhmQyMkAB3JP0po+BAgAgIXJ3xS3RPejQRsnIAHOoh99BxAAAFtIQGyS/uOBEwlGZzCGPgMIAIC97gjYp1cCpEgQkPZmJn0FEAAAe64EbFJxURDbAcz8ARAAAOvVBHyhvyZgIhJgr4I/9vwBAQDgdIC7pwMmcETQHkf9qPYHBADAYfcE9Nd7WZD2eBCXBVn6kh/O+QMCAODQGwNb6702WLsx8MZ2JMCC1/tywx8gAABOpnBz/Nu5iRMKdG0JSAko3jqFYLXOwz7c7Q8IAAD89IqgiqeE8zfFURxo8id9edUPAAEAKKsuYLjuugCtOJC6ALORK4mgjQMgAAAVrQa8mZs4IU9vXUBxClsCLPkDIAAAVpOAJ/KSYrezJWB5prDkD4AAAHiyJdAhJ3FCsd6jgte3TSWMvUuaJJA2DIAAAOhZDXhczuRX6K0N0K4Q5uIgwymUvC95jLYLgAAAqBKBl/I2TszUe1ywcMskgtoYVkj+TFsFQAAADLtBMGdDzE19JwVixPXt0whtNZyU1KFtAiAAAN5YDXg6f1PcEikCt/VuC3CLoMeckfSmyA8AAQDwyQVC+clxc/WuCOQlTWRFoGoz/k6Sx2mDAAgAgK9F4FcFyXGTcxJj7ul9ZpgTA+WSr73cJ/kH2hwAAgBgKuRM/mRu4gShoUsE5J8vTkEEHmIVbQwAAQAwrQCUPv+vzei1f+q5UbAgOZ46AQQAAAEAsIoAPPw+gCYDWqDrOTmgHSF08F0CCAAAAgBgLQEofQ9A7kb9MpAn/3zR1ilOkwEEAAABALCoAJRxTXDh5njXcUBPhUD7O7Q3B7THh27ZWwgQAAAEAMAeAqBRuupf2+vXlvk1IfC0dkDbKtDqBjQhuKE9S2yfx4gQAAAEAMCeAlAWN2WIa/+Zoi2TXTN9T7YPNJnQ7hrQxKBo62T5902z4vYBAgCAAAA4RwAqRM7uteeGtTDXZvs3tk9z/X3aEUJtBcBdtD+jXUSk/R03d0x3bSWY8BljBAAAAQBAADgFAAAIAAACgAAAAAIAgAAgAACAAAAgAAgAACAAAAgAAgCAAAAAAoAAACAAAIAAIAAACAAAIAAIAAACAAAIAAIAgAAAAAKAAAAgAACAACAAAAgAAAKAACAAAAgAgN0pKrr+WN6WaVcRAAQAAAEAcE74vyLZn7dtjkAAjOHWgS/2y2/8Z9obAAIAYIbg/7VktOS2RCAAxnHz6Hf35DfOk3Sh7QEgAAC+DP83JGla8JeAABgqAKLUt94g+V+0QwAEAMCbwf+EZLzkTunwRwC8KgAaBZKekkdolwAIAIDR4f+25OjDwY8A+EQAStgk+QvtEwABADAi+J+UxEnulhf+CIDPBECjSNJX8ijtFQABAFAV/gGSkxUFPwLgcwEoIUXyf2m3AAgAgJ7gf1oyVXLPnfBHAEwhABrXJQMk/0A7BkAAAKoa/iGSDHeDHwEwlQCUkCqpTnsGQAAA3An+30pmVjX4EQBTCoDGDckgya9o3wAIAEB54V9HctrT8EcATCkAJeyWvEA7B0AAAEoH/z9JPtUT/AiA6QVA45ZkqPZmA+0eEAA+AhD+YZJsFeGPAJheAErQ3hR4hfYPCACAM4P/d5KFqoIfAbCUAIif324YJXmc/gAIAIBzwr+J5ILq8PdUAIpTphDw3heAEg5pbzrQLwABALB38P+LZKkRwa9HAIq2Tibg3RGAY+uM+t20Nx3GaW880E8AAQCwX/hHSi4ZGf6eCkDB5ngC3g1unEoWBv9+2hsPb9NfAAEAsEfw/0HyldHB/3cBmF1lAchJnEDAuyMAWT964zfU3nqI1d5+oP8AAgBg3fBvLbnqrfDXyN8xr8oCoHErdTohXwnXL54QXvwtT0j86UeAAABYK/j/KFntzeC/LwA/LvJIAAq3TCLkK2LXHFFUWOjt31N7A2KK5Cn6FSAAAOYP//YXL1/L8UX4axTsX+mRAFxbP17c2jmDoPfuCQB30d6ECKF/AQIAYEJiZ33+eru+H194IaSp6PXRGJ+FReHZNI8EgGLAiim+nOVLARBZZ8/fO3vu4jz5r/8r/Q0QAAAT8HxIRJO3w6PPVfMPE8/4NXDxt4ZtfBoW1zbEeCwB17dNI/Afnv0f/sanv6fG+Omfi7816Sw+jplZ8NmX33al7wECAOCT0G/25HPBTeOfDWqcXxL6pdFk4OKlqz4LC08LAUu2Am5SEPh3ds8Vxdcu+FwAmnb9ULxSr819mnT54Hzt6D7t5b/mXQFAAAC8MNt//dmgJpur+YffLSv4SzNt/lLfBcalLBnm4z2WgJwNMUhASeX/+aM+D//M0+ceCP/SvNWw41X5z+GSP9FHAQEAUMxzIRHvVQ9slF1Z6Jemaad3fRoa+amfeSwASMDP5/4zU30e/hpLV28oVwBKcVvypSSIPgsIAIC+Zf4/PRfcdHm1gPCbVQn+El4MaerT0Ci8dknkJE5EAjwN//Stpgh/jXdHxLsjAKU5LOkl+S19GRAAgCoU9T0b1DjtGf+we54Ef2lS9xz0bXicP+7a09cjAVpB4c0dCVz566tjnQVFwr9Zt6oKQAkFkumS5+jbgAAAVFDUVz2wYa7e0L+Pf5gYM+VTnwdI4akd4qpeCdAKAx0iATdPJJom/DVS9xzyNPwfJlkSSdEgIAAAVSzqc5fqAQ3FC6HNxct1o0W/EfGmCBFNAq4pkIAbNpeAm8fXy+9VbCoBmPzpUlUCUMJ5igYBAQCK+lTN9iVSJMSLtaMeGGz9IrqK/Pwic0hAxo9qJGB7go2f+jVX+Gu0emeIagGgaBAQAKCoTy/V/MOF/HvlbL91uQPt9t0HTRMmhZl7FEnANJtd8/utKCosMl34n7twWbxWv61RAkDRICAAQFFflZf5AxuJF2u2cGtwnTT3C1OFSmHWPiTg4Vv+vP/Ij1t8/f1mb4R/WUWDzzN2AAIAFPWVKuqTf6d4qU6rKg2qLXt/bLpgKTxzUIZ4jG4JuG5xCbiVtkoUFRSYMvw1Bo1L8LYAlGYzRYOAAABFfT8X9XkykL5av63IPn/JfBJwNk3XmwEuNAmw6NsBtw6tkOGfb9rwLywsFqFRvXwpAKWLBkdQNAgIADi6qM9TVq7bZM6gyT6qXwJcDwhNtVb4H1wuivLzTBv+GvvSjpsh/MsqGgxmzAEEABxZ1OcJA8cmmDdszp+QEjBBtwQUW0QCbh1YKsM/19ThrzFz0UqzCQBFg4AAgD2L+l5ws6jPE0KierqWdE0bOBdOiZxEBRKQYm4JuLV/iSjOyzF9+Gt06D/KzAJA0SAgAOC8oj5P2XvwmLlD52Km7rcDfpKAKeYM/32LRHHuVUuE/+UrOeKNsPZWEACKBgEBAOsU9VXTWdTnKTMWfmX+8Ll0Wo0EbJ1isvD/XIb/ZUuEv8b3m3ZYLfwpGgQEAExe1FcrymcDY/v+I60RQFfOipyNsboloGjrZHOE/94FojjnomXCX2NE/BwrC0DposEVFA0CAgC2KurzhNfD2olLV3IsIgHnpATEWV8C9swXxdfOWyr8Neq17WcHASjNEUlvigYBAYCHl/kbWq2oz1PWJm2zTAgVXrsgcpIUSMAWH0nAnnmi+Gq25cL/yIlMu4V/WUWDzzL2AR/BubP9R+Vsf7gM6quql/lfqt3StAPg8Lg5lgqjwmuXRG5SvG4JKNwyybvhv/tTUXzljOXCX+Oz5d/aWQBKs0ESLnmUMREBAGcE/x9+Xua/pW6ZP0w8HxJhimX+ytCWdi0XSrmXRe6mSfolYLOXJGD3XFF8OcuS4a/R46PxThGAEtIl70n+iTESAQB7LvP7y9n57moql/m1av6akeIVL1fz6+Xw8QwLSsBVKQGTdUtAweZ4Y8N/1xxx/VK6ZcM/Jzdf/L9GHZ0mACUUSmZIqjFmIgBgjxn/X58NanxU5TK/Vtj3Yq0Wlh3o5i9fY82AyrsmcpOn6JeAZIMkYNdscf3iCcuGv8am7XucGv6luSOZyooAAgDW3uNfoHLGr6Et9Vttxv8w3QeNs25I5eeK3M3TzCcBu2aJ6+ePWTr8NcYlLEAA/s4lSXvJI4ypCABY5wx/VPWAhgVKi/sCG3vtxj6j0ZZ4taVe60pAnsjbkqBbAvKT4xQJgAz/c4ctH/4aTbp8QPD/km2SlxlbEQAw96z/z88GNd6vernfjMf59JKUssvaYVWQLyVgun4J2KRfAq5nH7RF+GecPkfYsy2AAIAFZ/3BTadX8w+7qzL8n3NV9kfbckAbm7DA+qFVUCjyts70qQTcOLPPFuGvsfSb9QQ92wIIAFiqur929cBGOSqDX7vExy7L/eXRuPMH9giuwiKRlzJLvwQkxVY9/E/vsk34a/QbEU/Au88W3hpAAMC3s/7Rqov8tNf5nDKIpWdl2yS8ikT+tjm6JSAvaaK4lTrdvfDPTLVV+OfnFwm/iK4Ee9W4IPFjLEYAwMsV/s8GNUlSGfwarjP9DhrAvvj6B3uF2PZPdUvAtfXjxfVtUysO/4xttvpuGtt3HyTQPX9wqDfjMgIA3gj/0GZ/rB7Y6LzqQj8zX99rFP2Gx9kuyPJT5+uWgJJjgrd2zvhl+J/abLtvpjFp7heEuT4+kzzBGI0AgHEz/1rVAhreVL3fb4UrfI1AW/LVln5tJwE7P1ciAa5tgY0TXS8K3kqdIW6eTLJl+Gu07P0xIa6f3ZJ/Z6xGAED92f5h7PerZ9uuA7YMtPzdS5RJgKtIcOcC24Z/9vlL4tX6bQlwNVyWhDBmIwCgAO24zXPBTTYo3+8Pbc5gJYmf84Vtg63w6EbXnr7u7YAD39j2G2ms+j6ZvqD+zoD+jN8IAOja72/+h2cDG2er3e8Pc+R+f3lE9frY1uFWdP6ExxcGaY8PFZ7eb+/vIxk4NoG+YAxfSH7DWI4AQNWL/Z6tHtDwutLwD2goXq7TmoGpFNrSr7YEbPeQK8za47o0qNIVAfnva8JQeGyT7b+J67sUFovQqF70BeNIkjzFmI4AgPvFfi/IsL6hPPzrEv5lsXKdM8Ku5PbAwowfRcHBb0T+rsWuo4P5Py50LfMXnkwRRXk5zvkWkr0Hj9EHvHNp0H9lbEcAoPLwf0l1pT/hXzEfjp3mqNCDvzNj4Vf0Ae+wXfKPjPEIAJQf/q9UCwhXe8yP8K+UkKierqVgAtF5tO8/kj7gPXbymBACAGXf6f+qDP9bhL9v2HPwKIHoMC5dyRGvh7Wj/XuXXZKnGfMRAPj7zP9P1VQX/PmHU/BXBWZ8/hWh6DDWJm2j7fuGdZJfMfYjAIR/SLMnqgc2uqL0nL921M/mr/mppv17Ix0XgIVH0kTehCGiMDvbkQIwPG4Obd93zGP8RwAcz7NBjY+pvuTnxVpRDDBVRFsK1paEnRJ+Bau+EDmN/yZyGrwuclvVEgXbNztOAOq17Ufb9y3DyQAEwMHh3+QHXvQzD9qSsO2D79JlkTdqgCv4HyDsDZE/O14U5Rc6IvwPH8+gzZuDTmQBAuC8u/2Dm05RHf7Ph0QwoOhgWOxsey/579slctuH/TL8S5Hbr50ozMywvQDMX76GNm+e54RDyQQEwEkV/xHP+IUpDf9ngxozmOikbpu+9gy8wmKRv2SuyGn4VoXhf5/IIFGwcZ2tBaDbwLG0efNwSfJHsgEBcELR3+9Vn/V3VfzXjWYgUcDh4+n2mvWfOyfyPurpXvA/RN7k0aIoN8924Z+Tmy/eatSR9m4ukiX/QEYgAPbe9w9sfEp50V9tiv5UMW/ZGvsU+qVuFbmta3sU/ve3BHq0EIXHj9lKANZv3klbNyejyQgEwM77/nOU7/uHNmPgUEi3QeNscN9/kcj/dIrICXtTV/jfp6mfKPh2hW0EYGT8XNq6ObknqU1WIAB23PcPU77vH8i+v2reathRXMvJt+6S/+kskfteBzXB//CWwNhBoujKVUuHf25uoWjYcQBt3dz1AH8iMxAAO+37/1b5637+YVzzaxBJKbusueSfvF7ktAgxJPzvbwl0aiQKD+23rAAcTDtJGzc/35MbCICdzvsnc97fOnwydb61gi03X+RPHWto8D9Ao7dF/vLPLRf+eXmFYu7ib2jj1iCa7EAA7LD0X091+LP0byzhHd6zzpL/yeMit1dL74V/6S2BoX1F0YWLlvlWFy9eEz0Hx9DGrcEVyT+TIQiAlZf+H60e2ChHtQC8zD3/hnMy/Yz5l/zXrhI5Ef4+Cf/7WwJt64nC3amWmP1nnb4gajTpQvu2DgvJEQTAylX/n1L1b03mLF5l3kC7liPyYj72afA/QPibIn/BDFFUWGTq2f/3Sam0betRhyxBAKw4+3+mmn/YPZXhXz2gIQOCl+j8/mhRUGC+QCs8fFDkdmlqnvAvvSXwQRdRePas6b5Zfn6ROHv2khg7dQFt23pkSJ4iUxAAx1/481LtlgwIXuJvjTuLrNPnzBVkXy0WOY3fNmX436dlTVGQkmSq73bp0jWXADTvPoi2bU3GkCkIgHWW/kMimikv/AtqwkDgZb5as0EUFBT7fq+/oEicX7nU3MH/0MuC17ZuNtXsP+1Iuni1flvatTUplvwb2YIAWILqgY0uKJ/9U/jndYbGzBDZ2Rd8GmBXr+aKw4dPiLSt20RO+FuWEYDsQ0fEhQtXfb6NculSjksAFn65ljZtbRLIFgTACrP/9sz+7UHjzu+LtLTjorDQN6sAZ89eEAcPHhUHDhxxkf1hD0sIwJX3u7pCVyM7+7LIySnw2cpJyf+Od0dMok1bm1uS/0PGIABmn/1fYfZvD16t30Zs3bZbnDt30cvL1oXixInM+8FfwrFVX1lCAC6s+vJ+8JZw+XKOFCnfzP7PnLkogiJ70Katz+dkDAJg5tl/D9Xh/1wws39fMuOz5a4leG+tAly5cs216vBw+JdwqWMjU4f/tWaB4mzG2V8IgMb581dce/Lenv1vStlDW7YHdyXPkTUIgFln/1z6YzN6fjTOFbznz18y9nifFIzTp8+VG/wlpM+eamoBuDxuSJnhX3pL4No14x9b0lYcSv47Y2ctpi3bh1VkDQJgxtl/X/Wz/6Z0eB8T0Kyb2L//iGsVwKiCtry8AnH8eHql4a9xcNdecbWpv2kF4NyWzRUKQAna0TyjVlW0kxul/7ui+wylLdsLVgEQANPN/rM5929Pvlmb5ArfrKxsA/apr4pDh465Ff4lnBk9yJThf7VjY7fCv4Rz56646h2MnP0fP3lavN6gHe3YXkwncxAAM93697Lq8JdCQUc3CaPj594PX+1Ynqol/6yss1UK/hKOJG40pQBcnDOtSgJQwtWrecrCXztxUPrvXv5NIm3YfhRIfkv2IABmee43ied+7UuLHoPuh69WoJebq28PW/vzR4+e8ij8Szjfp50pz/57IgAaFy/qvzNAe/AnO/vBv3fg2ATasD3pTfYgAGaY/T9RzT/8jsrwr+YfJht4NJ3cJGhLyKk7990PX60eICcnz6NZv3a2/9Cho7rCX+PEkoWmPfvvKT8VCOZ5HP7nzl3+xd9Zu/U7tGF7clTyCBmEAPj6xb+xyov/QiLo4CZj3uJVDxbjHTwqLly4XIXX6K64xEFv8N9nX5q40qq2qc/+e4p2XFALdHelqvSef2m2/3iQtmtvQskgBMB2F/9w9M989BsWW2YQHzuW7gr3sirac3MLXJJw/HiGuuAvRebkcaY/+68H7QnfnJz8Mr+tdqmQtt9f1qy/hGnzv6Tt2puVZBAC4MPl/4gQ5df+BjamY5uQmi17VRjGWiX/kSMnxYkTGeLUqSw52z9uSOiXJm1bqrjW8P+Z/uy/CrR3BTQh0ND+tTt/ptOA0bRde3NH8keyCAHwVfHfBor/nMO6DVsND/Wqkj24j2XO/nuT9Ixs8VbDDrRb+9OTLEIAfLP8H9CwSPnyf93WdGqTEpPwmekE4Oia1ZY6++8tvvl+M23WGSSSRQiAL5b/3+Tsv7No02eo6QRA42KXZpY7+280QyfOps06g9uS35FJCIC3q/+XqRaA50Ob06FNzJvhHcSu3QdNJwCn5s205Nl/Iwnv0J826xzakkkIgLer/6+qr/5n+d/sLPryW9MJwMHd+8XVZoGWPPtvBHv2H6OtOouvySQEwJuX//y78uX/gIZ0ZAvw4egpptwGOD12iKXP/qtkzuKvaavO4rrkKbIJAfDW8v905cv/Ic3oyBagXtt+phSAw5uSXUvyVj/7r4KeH42nrTqPpmQTAuCt5f8zvPznXDYm7zClBJx/r5Otzv57QtbpC6JGky60U+fxGdmEAHiFav5hd5UKgOvufzqxVZg0e7EpBeD48i8cffZf4/ukHbRRZ3KKbEIAvHH8z5/b/5xNpwGjTCkAB/YfFpfb1Hfs2X+NT6bMp406lz+QUQiA0fv/k9Xv//P4j5X4W+POYu/eNFNKQEZCrGPP/ms06zaQNupcGpFRCIDB1/823qtaAF6s1YLOazGWf/2DKQXg0M7ddy83fDvjSt1X0w2h3uvp2YeP3TFj+KcdSRev1m9L+3Qu48koBMDo638LuP4XhsRMN+c2gORE28bilP+zhpAeVc+0s//Pv1xL23Q2W8koBMDI8/9Pqw7/agHhdFwL0rjTANMKwMl6bxkmAKdqviLOnrloSgHoNyyOtsl9AI+TVQiAMfv/IRHtlRcABjWh41qQV+u3EVu37TLfrYDbdhoX/j9zZu8B04X/GSklQZE9aJvwJlmFABhVALhC+fO/3P9vWWbMX246AUj7aoXhAnB6zWrTCcCmlN20SdDoR1YhAEYVAJ5QXgBYO4pOa1F6DBpnvhsBEyYbLgBZM6eaTgAmzlxMmwSNeWQVAmDUDYC5PAAEJQQ06yr27zeXABwd8r7hApA5ZIDpBKD1O0Npk6CxiaxCAAy6ATD8jmoBoMNam6+/22iu2wC7tDRcADI6NjdV+B8/cVq83qAd7RE0ssgqBIATAOAVRsXPMdcJgHB/wwXgVJ03TSUAy77ZQFuEEu5IHiOzEADVVwDX5ApgeJjI7oPMcwJg5x7jw7/kJMCho6YRgA8/mUZbhNL8hcxCABSfAIgYqFoAngtuSme1ONrSc+rOfea4BXD1aq8JwOkfvjeNANRq/Q5tEUoTSmYhAKqPAC5W/gZAaDM6qw34dPEqc5wAmD3dawKQ9eksU4T/9h8P0gbhYTqTWQiA4iOATXYovwOgJm8A2IF+Qyea4wTAyI+8JgCZoz4yhQBMnbecNggP8wmZhQCoFYDAxmdUC8BLtVvSWW1AaFQvc5wA6NnWawKQ0b21KQSgQ/9RtEF4mCVkFgJg/keALHgHwEt1WokXakaK50OauV4x5CGjn1i3YYvvHwFqGuI1AUgP9/d5+KdnZIu3GnZwfNvT+qDWF7U+qfVNJhZtNpNZCIDaOwACGt5Q/wpgtGU6lVav8IxfWJn/f0g5cvygM37aZ74VgD0HxKnA570mABpnj6f7VAC+XrfZ0W1O63Na3yt7fAlzCYFDv80eMgsBUH0J0G31lwBFW2LGXz2wkXtFjSERjh2Mo/sM9e0JgLXrvBr+rpMASUk+FYChE2Y5tr1p4e5On3TJuezDDvs+x8gsBECxAITdddwtgHWjRbVyZxicbCjNm+EdxK7dB313AmD+HK8LQNbCz3wqAGEd3nNm+Ic2r/KFY1ZabVTAWTILAVDKM/5hSsNf+/vM3pGekzN6j4obnTfjcLFw+RrfnQAYO9zrApA5brjPwn/P/qPOXPaXfYs7Ryoll8xCANQKgOprgP3DbTnQuJYdAxs5cnD+YPRknwnAsT6dvC4AGe908JkAzF70tSPb2LNBjTl1VDm3ySwEQOU7AL9z2jsAWjUxjxxVjXpt+vruBECLul4XgPSmoT4TgO6DxjmyjelZiXwhtLmTvtWvyS4EQJUA/KdqAdCKc0y9/B/clDsOPGBj8g7vC8C+NHEq6EWvC8CpgOfE2fQzXg//rKzz4m9NOrP8zzZARfyO7EIAVD0E9JZyATD5Mrm7lf/l33IY6UgBmDR7sfdPACRu9H74lzwKtDXF6wKwbuN2R7Yt7ay/nScdivkz2YUAqBKA+k57CbB6Fav/Hb7ceJ9O/Ud6XQDSFi7wmQCcXrrE6wLwyeT5jmxberflHCYA1cguBEDNQ0AhEc2UC0AQAmBH3m7cSezdm+ZVATgy4ROfCUBm3FivC0BEt4EIAAJQGS+SXQiAKgGIRAAQAHdZtup7754AGNDDZwKQ0b+7V8P/0OFT4tX6bREABKAyXiK7EAAEAAHwOh+Pn+7dEwCtw30mAOlR9bwqAAuWf+fYdoUAIAAIAAKAAJicRp0GeE8A9h8WJ2u+6jMB0E4fnM067zUB6DssDgFAABAABAABQADMyav124it23Z5RQAOJm/xXfiXnATYucsr4X/mzEUR2Lw7AoAAIAAIAAKAAJiX6fOXeecEwNIlPheA0ytXeEUAkrbudnSbQgAQAAQAAUAALECPgWO9cwJgUozPBSBraqxXBGDizEUIAAKAACAACAACYG4CmnUV+/cfNv4EwMC+PheAjIF9vCIALXsPQQAQAAQAAUAAEADz8/V3Gw0XgOPtm/pcANLbNjI8/I8dzxKvNWiHACAACAACgAAgAOZnVNwcwwXgZN23fC4Ap2q+Is6euWioACz9eoPj2xMCgAAgAAgAAmARmncfZOwJgG2pvg//kpMAew8YKgAffDIVAUAAEAAEAAFAAKzBaw3aih2pe407AbBihWkE4PSa1YYKQK1WvREABAABQAAQAATAOsxdtNIwATicMMk0ApA1a5ph4b9t5wHaEgKAACAACAACYC36Dp1omAAcHTLANAKQKf+3GCUAU+ctpy0hAAgAAoAAIADWIjSqp3EnALq0NI0AZHRqbpgAtH9vJG0JAUAAEAAEAAGwHmvXbzHmBEC4n2kE4FTdtwwJ/1Pp2eLN8A60IwQAAUAAEAAEwHqMnzZf/QmAnXvME/4lJwEOHVUuAKvWJtOGEAAEAAFAABAAaxL9zhDlAnBo9WrTCcDpH75XLgBDJsyiDSEACAACgAAgANbkjfD2YtfuA2pPAMxKMJ0AZM2brVwAGrR7lzaEACAACAACgABYl4XL16g9ATDiI9MJQOaowUrDf/e+o7QdBAABQAAQAATA2rw/arLaEwA925pOADK6t1YqALMXraLtIAAIAAKAACAA1qZem75KBeBEkxDTCUB6uL9SAeg2cBxtBwFAABAABAABsD6Jm7arEYDdB8SpgOdMJwAaZ49nKAn/rKzz4u3GnWk3CAACgAAgAAiA9YmftUjNCYC160wZ/q6TAJs2KRGAtYnbaTMIAAKAACAACIA96Nh/pJoTAPPmmFYAshZ+pkQARk+eR5tBABAABAABQADswduNOom9e9P0nwAYM8y0ApA5foQSAWja5UPaDAKAACAACAACYB+WrfpetwAc69PJtAKQ0aej7vA/ePgUbQUBQAAQAAQAAbAXH49L0H8CILKOaQUgPSJUtwAsWP4dbQUBQAAQAAQAAbAXjToN0CcA+9LEqaAXTSsA2umEs+lndAlAn6GxtBUEAAFAABAABMBevFq/jdiSssvzEwAbEs0b/iWPAqVs8zj8z5y5KAKadaOtIAAIAAKAACAA9iNh3jKPBSBt4WemF4DTy5Z4LAAbt+yijSAACAACgAAgAPak+8CxHgvAkQmjTS8AmXFjPRaACTMW0UYQAAQAAUAAEAB74h/RVezff9izEwD9e5heADL6d/dYAFr2+pg2ggAgAAgAAoAA2JdV3yZ6dgKgdbjpBSA9qp5H4X/0eJZ4rUE72gcCgAAgAAgAAmBfRsbNrroA7D8sToW+YnoB0E4pnM06X2UBWPr1etoGAoAAIAAIAAJgb5p3H1RlATiYvMX84V9yEmDnrioLwPujp9I2EAAEAAFAABAAe/Nag7Zie+reqp0AWLrEMgJweuWKKgtAzZa9aRsIAAKAACAACID9mbtoZdVOAMTHWEYAsqbFVSn8U1IP0CYQAAQAAUAAEABn0GfIxKqdABjYxzICkDmob5UEYMqny2gTCAACgAAgAAiAMwhp0bNKAnC8fVPLCEB620ZVEoD2746kTSAACAACgAAgAM7hu/Wb3RaAk3XetIwAnKr5ijh75qJb4X/y1FnxZngH2gMCgAAgAAgAAuAcxk2d794JgJQd1gn/kpMA+w66JQCr1ibTFhAABAABQAAQAGcR/c4Q904ArFhhOQE4vWa1WwLwccxM2gICgAAgAAgAAuAs3ghvL3btPlD5CYBp8ZYTgKxZ09wSgPrt3qUtIAAIAAKAACAAzuPzZasrFYCjHw+wnABkDn2/0vDftfcIbQABQAAQAAQAAXAm74+aVPkJgM5RlhOAjE6RlQrArIWraAMIAAKAACAACIAzqdumb+UnAML8LCcAp+q+VakAdBs4ljaAACAACAACgAA4l8RN28s/AbBzj/XCv+QkQNqxcsM/M/O8eLtxZ35/BAABQAAQAATAucTNXFSuABz65hvLCsDpH34oVwDWJm7jt0cAEAAEAAFAAJxNx/4jyxWAw7MSLCsAWfNnlysAoybN47dHABAABAABQACczduNOok9ew+VfQJgxCDLCkDmqMHlCkCTLh/w2yMACAACYG6eD4kIQwAQAKNZunJd2ScAerSxrABk9IguM/wPpp3kN0cAEAAEwAoC0Ox3CAACYDSDxyWUKQAnmoRYVgDSG/qXKQCfLfuW3xwBMJI/k10IgDKq+YffQQAQACNp2LH/LwVg9wFxKuA5ywqAxtnjGb8QgD5DY/nNEQCjyCOzEAClVA9sdBUBQACMZnPKjw+eAFi71tLh7zoJsGnTA+F/5sxFEdCsG783AmAUKWQWAqAUGdgHEQAEwGgS5i198ATAvDmWF4CsRZ89IAAbt+zit0YAjGQmmYUAqD0JENx0GQKAABhN94FjHzwBMGao5QUgc/zIBwQgZvpCfmsEwEh6k1kIgOqTAAEIAAJgNP4RXcX+/YfvC8CxPp0sLwAZfTo+IABRvT7mt0YAjOQNMgsBUF8HENCwEAFAAIxm5beJfz8BEFnH8gKQHhF6P/yPHMsUr9Zvy++MABjFcbIKATBoG6DJagQAATCaEbGzfxKAfYfEqaAXLS8A2imGs+lnXALwxar1/MYIgJEMJqsQAKPuA/hTNf+wewgAAmAkzbsN/OkEwPpE64d/yaNAKdtdAvD+6Cn8xgiAURRL/kRWIQDGnQYIbvItAoAAGMlrDdqK7al7RdrCz2wjAKeXLXEJQGjLXvzGCIBRjCCjEACjVwH+WcWlQAgAVMSchV+JIzGjbSMAmXHjxNYd+/ltEQCjyJY8RUYhAN54HKjzM35hCAACYBh9hkwUx97rbhsByBjQQ0yeu4zfFgEwgtuSumQTAuDNewEmIwAIgFGEtOgpTrQOs40ApEfVE+3eHcFviwAYQUcyCQHwxamAbxAABMAokmq+bRsBOBz8sngjvD2/KwJA1T8CYKvtgN7V/MNvIwAIgGriQoNtIwDLgt/mN0UAVHJR0pgMQgDMUBj4788GNs5AABAAlbSt3dg2AjCoZl1+UwRAFcsl/0L2IACmuy742aAmSdUCwm8hAAiAXt6oFy0OBzxvCwGoWyeS3xQB0MNpSYzkZbIGATD7isCjz4VE9JQysEEGfVr1wEYXZOcrquYfdhcBgKqgLZ1bPfxTAl/ht0QA3OHez0v7eyXfSmZLhkn8JI+QLer5/1eB0x0kCyKxAAAAAElFTkSuQmCC
');

class OrganigramaController
{
    public function getOrganigramaPuestos()
    {
        $organigramaModel = new Organigrama;
        $organigrama =  $organigramaModel->estructuraOrganigrama();

        $organigramaDesgloce = array();
        $organigramaLista = array();
        $padreId = "";
        $lastParentId = "";
        $lastDptoId = "";
        $lastSucursalPadre = "";
        //DEV
        foreach ($organigrama as $i => $puesto) {
            $personalAdscrito = $organigramaModel->getNodoOrganigrama($puesto['parentId'], $puesto['id']);
            foreach ($personalAdscrito as $j => $personal) {
                $index = $puesto['id'].'-'.$puesto['iddepa_padre'].'-'.$personal['idSucursal'];
                $indexPadre = $puesto['parentId'].'-'.$puesto['iddepa_padre'].'-'.$personal['idSucursal'].'-'.$personal['nip'];
                
                if ( !isset($organigramaDesgloce[$index] ) ) {
                    $organigramaDesgloce[ $index] =   array( array(              
                                                'id' => $puesto['id'].''.$puesto['iddepa_hijo'].''.$personal['idSucursal'],
                                                'parentId' => $puesto['parentId'].''.$puesto['iddepa_padre'].''.$personal['idSucursal'],                                                  
                                                'descripcion' => $personal['descripcion'],
                                                'contrato' => $personal['contrato'],
                                                'nip' => $personal['nip'],
                                                'nombre' => $personal['nombre'],
                                                'sueldo' => $personal['sueldo'],
                                                'curp' => $personal['curp'],
                                                'nss' => $personal['nss'],
                                                'sucursal' => $personal['sucursal'],
                                                'fechainiciolab' => $personal['fechainiciolab'] ) );
                }else{
                 array_push($organigramaDesgloce[$index],   array(             
                            'id' => $puesto['id'].''.$puesto['iddepa_hijo'].''.$personal['idSucursal'],
                            'parentId' => $puesto['parentId'].''.$puesto['iddepa_padre'].''.$personal['idSucursal'],   
                            'descripcion' => $personal['descripcion'],
                            'contrato' => $personal['contrato'],
                            'nip' => $personal['nip'],
                            'nombre' => $personal['nombre'],
                            'sueldo' => $personal['sueldo'],
                            'curp' => $personal['curp'],
                            'nss' => $personal['nss'],
                            'sucursal' => $personal['sucursal'],
                            'fechainiciolab' => $personal['fechainiciolab'] ));
                }

            }
            
        }


        // var_dump( $organigramaDesgloce);
        foreach ($organigramaDesgloce as $i => $empleados) {
            foreach ($empleados as $idx => $empleado) {
                array_push( $organigramaLista, $empleado);   
            }
            
        }
        // var_dump( $organigramaLista);
        foreach ($organigrama as $i => $puesto) {
            
            $personalAdscrito = $organigramaModel->getNodoOrganigrama($puesto['parentId'], $puesto['id']);
            if ( sizeof( $personalAdscrito) > 0) {
                $organigrama[$i]['descripcion'] = $personalAdscrito[0]['descripcion'];
                $organigrama[$i]['contrato'] = $personalAdscrito[0]['descripcion'];
                $organigrama[$i]['nip'] = $personalAdscrito[0]['nip'];
                $organigrama[$i]['nombre'] = $personalAdscrito[0]['nombre'];
                $organigrama[$i]['sueldo'] = $personalAdscrito[0]['sueldo'];
                $organigrama[$i]['curp'] = $personalAdscrito[0]['curp'];
                $organigrama[$i]['nss'] = $personalAdscrito[0]['nss'];
                $organigrama[$i]['sucursal'] = $personalAdscrito[0]['sucursal'];
                $organigrama[$i]['fechainiciolab'] = $personalAdscrito[0]['fechainiciolab'];
                $organigrama[$i]['idhijo'] = $puesto['id'];
                $organigrama[$i]['idpadre'] = $puesto['parentId'];
                $organigrama[$i]['contrato'] = $personalAdscrito[0]['contrato'];
                $organigrama[$i]['foto'] = OrganigramaController::convertImage($personalAdscrito[0]['foto']);

                
                

            } else {
                $organigrama[$i]['contrato'] = "-";
                $organigrama[$i]['nip'] = "-";
                $organigrama[$i]['nombre'] = "VACANTE";
                $organigrama[$i]['sueldo'] = "-";
                $organigrama[$i]['curp'] = "-";
                $organigrama[$i]['nss'] = "-";
                $organigrama[$i]['sucursal'] = "-";
                $organigrama[$i]['foto'] = PERFIL_GEN;
            }
            
        }
        return $organigrama;
    }

    public function getOrganigramaV2( $pamamsOrg )
    {
        extract( $pamamsOrg);
        $personalEncargado = $personal;
        $nodoPadre = $personalEncargado;
        $idPadreAnterior = '';
        $organigrama = new Organigrama;
        $estructuraOrganigrama = $organigrama->estructuraOrganigrama($tipoAbs, $elemento);

        $organigramaEstructurado = array();
        foreach ($estructuraOrganigrama as $i => $nodo) {
            $personalAdscrito = $organigrama->getHijosSubNodosPosibles(array(
                'hijo' =>$nodo['id'],
                'padre' => $nodo['parentId'],
                'depaHijo' => $nodo['iddepa_hijo'],
                'depaPadre' => $nodo['iddepa_padre'],
                'abstraccion' => $tipoAbs,
            ));
        
            foreach ($personalAdscrito as $j => $personal ) {
                // var_dump( $nodoPadre );
                // echo "<br>";
                if ( $nodoPadre != '' && $nodoPadre != '-'  ) {
                    
                    $idPersonal = explode('_', $nodoPadre );
                    
                    if (  isset( $idPersonal[1]  ) ) {
                        if ( $personal['nip'] == $idPersonal[1] ) {
                            //Limpiando la variable de nodoPadre
                            $nodoPadre = '-';
                            $idPadreAnterior = $personalEncargado;
                            
                        }else if($personal['id'] == $idPersonal[0] ){
                            
                            // echo "mames<br>";
                            continue;
                        }
                    } else {
                        // echo $personal['sucursal']."         ".$personal['nip'];
                        // echo $personal['nip']."<br>";                        
                        continue;
                    }
                }else{
                    if ( $nodoPadre == '-') {
                        //obteniendo que el personal encargado del subordinado sea el que se seleccionó en el select del lado del cliente
                        $personalJefe =$organigrama->personalPadreSucursal($personal['parentId'],$personal['iddepa_padre'],1,$personal['idSucursal']);
                        if ( sizeof( $personalJefe)  > 0 ) {
                            if ( ( $idPadreAnterior != ($personalJefe[0]['id'].'_'.$personalJefe[0]['nip']) && $idPadreAnterior != '' ) ) {
                                continue;
                            }                        
                        }else{
                                
                            if ( $personal['parentId'] != $elemento  || $personal['parentId'] == $personal['parentId']) {
                                
                                continue;
                            }
                            echo $personal['nombre']."<br>";
                            // echo $personal['nombre']."<br>";
                        }   
                    }

                    // var_dump( $personal);
                    // echo $idPadreAnterior."   ----------   ".$personal['parentId']."<br>";

                }
                $personalJefe =$organigrama->personalPadreSucursal($personal['parentId'],$personal['iddepa_padre'],1,$personal['idSucursal']);
                         
                // var_dump( $personalJefe );
                // echo "<br><br>";
                if ( sizeof($personalJefe) > 0 ) {
                    $personalAdscrito[$j]['id'] = utf8_encode($personal['id']."_".$personal['nip'] );
                    $personalAdscrito[$j]['parentId'] = utf8_encode($personalJefe[0]['id']."_".$personalJefe[0]['nip'] );

                    $personalAdscrito[$j]['foto'] =utf8_encode( OrganigramaController::convertImage( $personal['foto']) );
                } else {
                    $personalJefe =$organigrama->personalPadreSucursal($personal['parentId'],$personal['iddepa_padre'],1);        
                    
                    if ( sizeof($personalJefe)  > 0) {
                        $personalAdscrito[$j]['id'] = utf8_encode($personal['id']."_".$personal['nip']);
                        $personalAdscrito[$j]['parentId'] = utf8_encode($personalJefe[0]['id']."_".$personalJefe[0]['nip']);
                        $personalAdscrito[$j]['foto'] = utf8_encode(OrganigramaController::convertImage( $personal['foto']));    
                    } else {
                        // var_dump($personal['parentId']);echo"<br><br>";
                        if ( $personal['parentId'] == NULL) {
                            $personalAdscrito[$j]['id'] = utf8_encode($personal['id']."_".$personal['nip']);
                            $personalAdscrito[$j]['parentId'] = NULL;     
                            $personalAdscrito[$j]['foto'] = utf8_encode(OrganigramaController::convertImage( $personal['foto']));                                
                        }else{
                        $personalJefe =$organigrama->personalPadreSucursal($personal['parentId'],$personal['iddepa_padre'],1); 
                        
                        if ( sizeof($personalJefe) > 0 ) {
                            $personalAdscrito[$j]['id'] = utf8_encode($personal['id']."_".$personal['nip']);
                            $personalAdscrito[$j]['parentId'] = utf8_encode($personalJefe[0]['id']."_".$personalJefe[0]['nip']);
                            $personalAdscrito[$j]['foto'] = utf8_encode(OrganigramaController::convertImage( $personal['foto']));    
                        } else {
                            //puesto vacante 
                            $tamanioOrganigrama = sizeof($personalAdscrito);
                            $personalJefe =$organigrama->personalPadreSucursal($personal['parentId'],$personal['iddepa_padre'],99);
                            if ( isset($personalJefe[0]) ) {
                                $personalAdscrito[$tamanioOrganigrama]['id'] = utf8_encode($personalJefe[0]['id']."_".$personalJefe[0]['nip']);
                                //Obteniendo el puesto superior generico del puesto vacante
                                $personalVacanteJefe =$organigrama->personalPadreSucursal($personalJefe[0]['parentId'],$personalJefe[0]['iddepa_padre'],1);                                
                                $personalAdscrito[$tamanioOrganigrama]['parentId'] = utf8_encode($personalVacanteJefe[0]['id']."_".$personalVacanteJefe[0]['nip']);                                
                                $personalAdscrito[$tamanioOrganigrama]['descripcion'] = utf8_encode($personalJefe[0]['descripcion']);
                                $personalAdscrito[$tamanioOrganigrama]['nombre'] = 'VACANTE';
                                $personalAdscrito[$tamanioOrganigrama['fechainiciolab']]="";
                                $personalAdscrito[$tamanioOrganigrama['nss']]="";
                                $personalAdscrito[$tamanioOrganigrama['curp']]="";

                                $personalAdscrito[$tamanioOrganigrama]['foto'] = utf8_encode(OrganigramaController::convertImage( 'foto.png'));
                                array_push( $organigramaEstructurado, $personalAdscrito[$tamanioOrganigrama]);
                                //Puestos dependientes del puesto vacante
                                $personalJefe =$organigrama->personalPadreSucursal($personal['parentId'],$personal['iddepa_padre'],99);
                                $personalAdscrito[$j]['id'] = utf8_encode($personal['id']."_".$personal['nip']);
                                $personalAdscrito[$j]['parentId'] = utf8_encode($personalJefe[0]['id']."_".$personalJefe[0]['nip']);
                                $personalAdscrito[$j]['foto'] =utf8_encode( OrganigramaController::convertImage( $personal['foto']));  
                                $personalAdscrito[$j]['curp'] = '';
                                $personalAdscrito[$j]['nss'];                                
                            }
                        }
                           

                        }
         
                    }
                           
                }
                array_push( $organigramaEstructurado, $personalAdscrito[$j]);
                
            }

        }


        //Eliminando nodos innecesarios cuando se hace una abstraccion especifica
        if ( $elemento != '') {
            foreach ($organigramaEstructurado as $i => $nodo) {
                if ( ( strpos($nodo['id'], $elemento.'_' ) === false  &&  strpos($nodo['parentId'], $elemento.'_' ) !== false) ) {

                }elseif (strpos($nodo['id'], $elemento.'_' ) !==  false  &&  strpos($nodo['parentId'], $elemento.'_' ) === false ) {
                    
                }elseif( $nodo['parentId'] == null){
                        
                }else{
                    
                    unset( $organigramaEstructurado[$i] );                     
                }
            }     
            //Reacomodando indices
            $organigramaTemp = array();
            foreach ( $organigramaEstructurado as $nodo) {
            
                array_push($organigramaTemp, $nodo);
            }       
            $organigramaEstructurado = $organigramaTemp;
            
        }
        
        // var_dump( $organigramaEstructurado);
        foreach ($organigramaEstructurado as $i => $organigrama) {
            $padreEncontrado = false;
            foreach ($organigramaEstructurado as $j => $organigramaBuscar) {
                if ( $organigrama['parentId'] === $organigramaBuscar['id']) {
                    $padreEncontrado = true;
                }
            }
            
            if ( !$padreEncontrado) {
                $organigramaEstructurado[$i]['parentId'] = NULL;
            }
        }
        

        if($tipoAbs == "Jef"){
                $estructura = array();
                foreach ( $organigramaEstructurado as $i => $nodo) {
                    if ( $nodo['id'] != $personalEncargado  && $nodo['parentId'] != $personalEncargado) {
                        unset($organigramaEstructurado[$i] );
                    }else{
                        array_push( $estructura, $nodo);
                    }

                }
                $organigramaEstructurado = $estructura;
        }
        return $organigramaEstructurado;
    }

    public function buscaNodosPadres($padreId, $depaPadre, $statusTrabajador, $sucursal = null)
    {
        # code...
    }

    public function getIncidenicasTrabajador( $trabajadorId, $contratoId)
    {
        $mesActual = date('m');
        $incidencias = new Incidencias;
        $desgloceIncidencia['incidencias'] = $incidencias->getDeducciones($contratoId, $mesActual);
        $asistencias = $incidencias->getRetardosEinasistencias($trabajadorId, $mesActual, date('Y') );
            echo json_encode( $asistencias );
            exit();
        $listaFaltas = array();
        if ( sizeof($asistencias) ) {
            $diaActual = date('d');
            $tmpAsistencia = $asistencias;
            $listaAsistencia = array();
            for ($j=1; $j < $diaActual ; $j++) { 
                $diaIterado = date('D', strtotime(date('Y-m')."-$j" ) );
                $fechaIterada = $j < 10 ? date('Y-m')."-0$j" : date('Y-m')."-$j" ;
                $fechaFalta = "";
                if ( $diaIterado  !=  'Sun'){
                    
                    foreach ($tmpAsistencia as $l => $asistencia) {
                        $asistenciaExplode = explode(" ",$asistencia['timecheck']);
                        $fechaExplode= explode("-", $asistenciaExplode[0]);
                        $listaAsistencia[$asistencia['timecheck']]['fecha'] = $fechaExplode[2]."/".$fechaExplode[1]."/".$fechaExplode[0];
                        $listaAsistencia[$asistencia['timecheck']]['hora'] = $asistencia['hora'];
                        $listaAsistencia[$asistencia['timecheck']]['minuto'] = $asistencia['minuto'];

                        $fechaAsistencia= $fechaExplode[0]."-".$fechaExplode[1]."-".$fechaExplode[2];    
                        // echo $fechaIterada."    ".$fechaAsistencia."<br>";                   
                        if ( strtotime($fechaIterada) == strtotime($fechaAsistencia) ){
                            $fechaFalta = "";
                            unset($tmpAsistencia[$l]);
                            break;
                        }else{
                            $fechaFalta =$j < 10 ? $fechaIterada : $fechaIterada;
                            
                        }
                    }
                    if ( $fechaFalta != '') {
                        $fechaFaltaExplode = explode('-' ,$fechaFalta);
                        $falta = $fechaFaltaExplode[2]."/".$fechaFaltaExplode[1]."/".$fechaFaltaExplode[0];
                         $listaAsistencia[$fechaFalta]['fecha'] = $falta;
                        
                    }
                }
            }
            // foreach ($asistencias as $i => $asistencia) {
            //     if ( isset($asistencia['timecheck'])) {
            //         $asistenciaExplode = explode(" ",$asistencia['timecheck']);
            //         $fechaExplode= explode("-", $asistenciaExplode[0]);
            //         $asistencias[$i]['fecha'] = $fechaExplode[2]."/".$fechaExplode[1]."/".$fechaExplode[0];
            //     }

            // }
            // asort($asistencias);
            ksort($listaAsistencia);
            $asistencias = array();
            foreach ($listaAsistencia as $index => $asistencia) {
                array_push($asistencias, $asistencia);
            }
            $desgloceIncidencia['asistencia'] = $asistencias;
            // $desgloceIncidencia['faltas'] = $listaFaltas;
        }else{
            $desgloceIncidencia['asistencia'] = array();
            $desgloceIncidencia['faltas'] = $listaFaltas;
        }
        
        return $desgloceIncidencia;
    }

    
    public function getTrabajadoresNodo( $padre, $hijo, $nip = NULL)
    {
        $incidencias = new Organigrama;
        if ( $nip != NULL) {
            $trabajadores = $incidencias->getNodoOrganigrama($padre, $hijo, $nip);
            $trabajadores[0]['foto'] = OrganigramaController::convertImage( $trabajadores[0]['foto']);
            return $trabajadores;
        }
        else{
            $trabajadores = $incidencias->getNodoOrganigrama($padre, $hijo, $nip);
            foreach ($trabajadores as $i => $trabajador) {
                $trabajadores[$i]['foto'] = OrganigramaController::convertImage( $trabajador['foto']);
            }
            
            return $trabajadores;
        }
    }

    public function getTrabajadoresSucursalNodo($padre, $sucursal)
    {
            $incidencias = new Organigrama;
            $trabajadores = $incidencias->getNodoOrganigramaSucursal($padre, $sucursal);
            $trabajadores[0]['foto'] = OrganigramaController::convertImage( $trabajadores[0]['foto']);
            return $trabajadores;
    

    }

    public function getJerarquiaOrganigrama($opcion)
    {
        $organigrama = new Organigrama;
        return $organigrama->getJerarquizacionPuestos( $opcion );
    }

    public function getJefePuesto( $puesto)
    {
        $organigrama = new Organigrama;
        return $organigrama->getJefePuestos( $puesto );
    }

    public function convertImage( $uri)
    {
        $incidencias = new Organigrama;
        if ( $uri != 'foto.png' && $uri != '') {
            return "data:image/png;base64,".base64_encode( file_get_contents($uri));
        } else {
            return PERFIL_GEN;
        }
        
    }

    public function getStructuraOrganigramaSGC($departamento = null,  $sucursal = '' )
    {
        $organigrama = new Organigrama;
        $listadoPuestosPadreHijo =  $organigrama->getOrganigramaByDepartamento();
        $estructuraOrganigrama = [];

        foreach ( $listadoPuestosPadreHijo as $i => $puestoPadreHijo) {
            if ( $departamento == "General") {
                if ( $sucursal != '') {
                    unset( $listadoPuestosPadreHijo[$i] );
                } else{
                    array_push( $estructuraOrganigrama, $puestoPadreHijo );
                }
                
            }else {
                if ( $sucursal != '' ) {
                    if ( $departamento == 'js' && $sucursal == $puestoPadreHijo['idsucursal']  ) {
                        if ( $puestoPadreHijo['puestoHijo'] == 'JEFE DE SUCURSAL'  || $puestoPadreHijo['puestoPadre'] == 'JEFE DE SUCURSAL' ) {
                            $departamento = $puestoPadreHijo['idDepaHijo'];
                            
                        }
                    }elseif( ( $puestoPadreHijo['puestoHijo'] == 'JEFE DE SUCURSAL'  || $puestoPadreHijo['puestoPadre'] == 'JEFE DE SUCURSAL'  ) && $departamento  != $puestoPadreHijo['idDepaHijo'] ){
                        $departamento = $puestoPadreHijo['idDepaHijo'];
                    }
             
                    // echo $departamento . "  !=  ".$puestoPadreHijo['idDepaHijo']."<br>";
                    if ( $departamento !=  $puestoPadreHijo['idDepaHijo'] ) {
                        
                        unset( $listadoPuestosPadreHijo[$i] );
                    }elseif( $sucursal == $puestoPadreHijo['idsucursal']){

                        array_push( $estructuraOrganigrama, $puestoPadreHijo );
                    }
                    // echo "$sucursal -- ---- ".$puestoPadreHijo['idsucursal']."<br>";
                } else {
                    if ( $puestoPadreHijo['idsucursal'] != $sucursal  && $sucursal != '') { //Quitando del arreglo los puestos que no pertenecen a la sucursal 
                        
                        unset( $listadoPuestosPadreHijo[$i] );
                    }else{
                        // echo $puestoPadreHijo['idDepaHijo'] ."   ---  ".$departamento."      ".$puestoPadreHijo['puestoHijo'] ."<br>";
                        if ( $puestoPadreHijo['idDepaHijo'] == $departamento) {
                            if ( $puestoPadreHijo['depaHijo'] == 'VENTAS') {
                                if ( strpos($puestoPadreHijo['abstraccion'], 'Ger' ) !== false ) {
                                    
                                    $puestoPadreHijo['id'] = $puestoPadreHijo['id']."_".$puestoPadreHijo['idDepaHijo']."_".$puestoPadreHijo['idsucursal'];
                                    $puestoPadreHijo['parentId'] = $puestoPadreHijo['parentId']."_".$puestoPadreHijo['idDepaPadre']."_";
                                    array_push( $estructuraOrganigrama, $puestoPadreHijo );
                                }
                            }else{
                                    array_push( $estructuraOrganigrama, $puestoPadreHijo );
                            }
                            
                        }

                    }                    
                }
            }
            
        }


        return $estructuraOrganigrama;
    }

    public function generadorEstructura( $puesto )
    {
        $financierosModelo = new EdoFinancieros;
        
        $puesto['idsucursal'] = $puesto['idsucursal'] == null || $puesto['idsucursal'] == '' ? '--' : $idsucursal['idsucursal'];
        
        $sucursal = $financierosModelo->getSucursal($puesto['idsucursal'], '---');
        $estructura = [
                        'key' => $puesto["id"]."_".$puesto['idDepaHijo'],
                        'departamento' => $puesto['depaHijo'],
                        'puesto' => $puesto['puestoHijo'],
                        'sucursal' => $puesto['idsucursal'] != '--' ? $sucursal[0]['descripcion'] : ''
        ];
        if ( $puesto['parentId'] != null && $puesto['parentId'] != '') {
            $estructura['parent'] = $puesto['parentId']."_".$puesto['idDepaPadre'];
        }

        return $estructura;
    }

    public function getDepartamentos( $departamento )
    {
        $modeloSucursal = new Sucursales;

        return $modeloSucursal->getDepartamentos( $departamento );
    }
    
    public function getSucursales(  )
    {
        $modeloSucursal = new EdoFinancieros;

        return $modeloSucursal->getSucursal( );
    }
}




if( isset($_GET['op']) ){
    $op = $_GET['op'];
    switch ($op) {
        case 'getOrganigrama':
            
            echo  json_encode(OrganigramaController::getOrganigramaV2($_GET),256 );
            //  echo json_last_error_msg ();
            break;
        case 'deducciones':
            echo json_encode( OrganigramaController::getIncidenicasTrabajador($_GET['empleado'], $_GET['contrato']) );
        break;
        case 'trabajadoresNodo':
            if( !isset($_GET['nip']) ){
                $hijo = explode( '_', $_GET['hijo']);
                if (isset( $hijo[1])) {
                    echo json_encode( OrganigramaController::getTrabajadoresSucursalNodo($_GET['padre'], $hijo[1]) );
                }else{
                    echo json_encode( OrganigramaController::getTrabajadoresNodo($_GET['padre'], $hijo[0]) );
                }
                
                
            }else{

                echo json_encode( OrganigramaController::getTrabajadoresNodo($_GET['padre'], $_GET['hijo'], $_GET['nip']) );
            }
            break;
            
        
        case 'getJerarquiaPuestos':
                 echo json_encode( OrganigramaController::getJerarquiaOrganigrama($_GET['selJerarquia']) );
            break;
        case 'getJefePuesto':
                    echo json_encode( OrganigramaController::getJefePuesto($_GET['selPuesto']));
            break;
        case 'getOrgSGC':
                    echo json_encode( OrganigramaController::getStructuraOrganigramaSGC($_GET['departamento'], $_GET['sucursal']) );
            break;
        case 'getDepartamento':
                echo json_encode( OrganigramaController::getDepartamentos( $_GET['departamento'] ) );
            break;
        case 'getSucursales':
                echo json_encode( OrganigramaController::getSucursales() );
            break;
    }
}else{
    switch ($_POST['op']) {
        case 'subir':
        $trabajador = new Trabajador;
        $uriFoto = $_SERVER['DOCUMENT_ROOT']."/intranet/Documentos/Trabajadores/".$_POST['trabajador']."/".$_FILES['input1']['name'];
        $trabajador->setFotoTrabajador($_POST['trabajador'],$uriFoto );
        move_uploaded_file($_FILES["input1"]["tmp_name"], $uriFoto );
          $decoficado64 = base64_encode (file_get_contents( $uriFoto));
       echo  "data:image/png;base64,$decoficado64";
            break;
        
        default:
            # code...
            break;
    }
    // OrganigramaController::getOrganigramaV2();
    // echo $decoficado64;
}


