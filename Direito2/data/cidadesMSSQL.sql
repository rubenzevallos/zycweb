SELECT ', (''' + munName + ''''
+ ', ' + CONVERT (VARCHAR, munLatitude)
+ ', ' + CONVERT (VARCHAR, munLongitude)
+ ', ' + CONVERT (VARCHAR, munAltitude)
+ ', ' + CONVERT (VARCHAR, munArea)
+ ', ' + CONVERT (VARCHAR, ANOINST)
+ ', ' + CASE WHEN AMAZONIA = 'S' THEN '1' ELSE '0' END
+ ', ' + CASE WHEN FRONTEIRA = 'S' THEN '1' ELSE '0' END
+ ', ' + CASE WHEN CAPITAL = 'S' THEN '1' ELSE '0' END
+ ', ' + CONVERT (VARCHAR, MESOCOD)
+ ', ' + CONVERT (VARCHAR, MICROCOD)
+ ', ' + CASE SITUACAO WHEN 'ATIVO' THEN '1' WHEN 'IGNOR' THEN '2' ELSE '0' END
+ ', ''' + munState + ''''
+ ', ' + CONVERT (VARCHAR, UFCOD)
+ ', ' + CONVERT (VARCHAR, MUNCOD)
+ ', ' + CONVERT (VARCHAR, MUNCODDV) +
+ ', ' + CEP + ')'
FROM geoBRMunicipios
LEFT JOIN CADMUN ON MUNCOD = munCode
LEFT JOIN LOG_LOCALIDADE ON munName = LOC_NOSUB
ORDER BY munState, munNome