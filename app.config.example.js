export const env = 'dev'

export const netConfig = {
  ioScheme: 'http',
  ioDomain: 'bigcrash-backend.example',
  ioPort: 8443,

  httpScheme: 'http',
  httpDomain: 'bigcrash-backend.example',
  httpPort: 80
}

export const ioUrl = `${netConfig.ioScheme}://${netConfig.ioDomain}:${netConfig.ioPort}`

export const httpUrl = `${netConfig.httpScheme}://${netConfig.httpDomain}`

