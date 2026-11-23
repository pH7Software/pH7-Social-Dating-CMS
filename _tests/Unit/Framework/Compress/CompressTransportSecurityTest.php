<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Framework\Compress {
    final class SocketClientSpy
    {
        public static array $aCall = [];
    }

    function stream_socket_client(
        $sAddress,
        &$iErrorCode = null,
        &$sErrorMessage = null,
        $fTimeout = null,
        $iFlags = 0,
        $oContext = null
    ) {
        SocketClientSpy::$aCall = [
            'address' => $sAddress,
            'timeout' => $fTimeout,
            'flags' => $iFlags,
            'context' => stream_context_get_options($oContext)
        ];

        return false;
    }
}

namespace PH7\Test\Unit\Framework\Compress {
    use PH7\Framework\Compress\Compress;
    use PH7\Framework\Compress\SocketClientSpy;
    use PHPUnit\Framework\TestCase;
    use ReflectionClass;

    final class CompressTransportSecurityTest extends TestCase
    {
        public function testRemoteCompilerUsesVerifiedTls(): void
        {
            $oReflection = new ReflectionClass(Compress::class);
            $oCompress = $oReflection->newInstanceWithoutConstructor();
            $oReflection->getProperty('bIsGoogleClosure')->setValue($oCompress, true);

            $mSocket = $oReflection->getMethod('googleClosureEligible')->invoke(
                $oCompress,
                'const value = 1;',
                'const%20value%20%3D%201%3B'
            );

            $this->assertFalse($mSocket);
            $this->assertSame('tls://closure-compiler.appspot.com:443', SocketClientSpy::$aCall['address']);
            $this->assertSame(10, SocketClientSpy::$aCall['timeout']);
            $this->assertSame(STREAM_CLIENT_CONNECT, SocketClientSpy::$aCall['flags']);
            $this->assertSame(
                [
                    'SNI_enabled' => true,
                    'peer_name' => 'closure-compiler.appspot.com',
                    'verify_peer' => true,
                    'verify_peer_name' => true
                ],
                SocketClientSpy::$aCall['context']['ssl']
            );
        }
    }
}
